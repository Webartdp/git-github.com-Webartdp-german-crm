<?php

class CrmTime
{
    /** @var modX $modx */
    public $modx;

    /** @var array $config */
    public $config = array();

    public function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption(
            'crmtime.core_path',
            $config,
            $this->modx->getOption('core_path') . 'components/crmtime/'
        );

        $assetsUrl = $this->modx->getOption(
            'crmtime.assets_url',
            $config,
            $this->modx->getOption('assets_url') . 'components/crmtime/'
        );

        $this->config = array_merge(array(
            'namespace'      => 'crmtime',
            'corePath'       => $corePath,
            'modelPath'      => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath'  => $corePath . 'templates/',
            'assetsUrl'      => $assetsUrl,
            'cssUrl'         => $assetsUrl . 'css/',
            'jsUrl'          => $assetsUrl . 'js/',
            'connectorUrl'   => $assetsUrl . 'connector.php',
        ), $config);

        $this->modx->addPackage('crmtime', $this->config['modelPath']);
        $this->modx->lexicon->load('crmtime:default');
    }

    public function getAssignmentUserId($assignmentId)
    {
        $assignmentId = (int)$assignmentId;
        if ($assignmentId <= 0) {
            return 0;
        }

        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $assignmentId,
        ));

        if (!$assignment) {
            return 0;
        }

        return (int)$assignment->get('user_id');
    }

    public function getTimesheetUserId($timesheet)
    {
        if (!is_object($timesheet)) {
            $timesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => (int)$timesheet,
            ));
        }

        if (!$timesheet) {
            return 0;
        }

        return $this->getAssignmentUserId($timesheet->get('assignment_id'));
    }

    protected function buildTimesheetRange(CrmTimesheet $timesheet)
    {
        $workDate = (string)$timesheet->get('work_date');
        $startTime = (string)$timesheet->get('start_time');
        $endTime = (string)$timesheet->get('end_time');

        if ($workDate === '' || $startTime === '' || $endTime === '') {
            return false;
        }

        $startTs = strtotime($workDate . ' ' . $startTime);
        $endTs = strtotime($workDate . ' ' . $endTime);

        if (!$startTs || !$endTs) {
            return false;
        }

        return array(
            'id' => (int)$timesheet->get('id'),
            'assignment_id' => (int)$timesheet->get('assignment_id'),
            'work_date' => $workDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_ts' => $startTs,
            'end_ts' => $endTs,
        );
    }

    protected function createViolation($userId, $timesheetId, $relatedTimesheetId, $direction, $restHours, $requiredHours, $message)
    {
        $violation = $this->modx->newObject('CrmViolation');
        $violation->fromArray(array(
            'user_id' => (int)$userId,
            'timesheet_id' => (int)$timesheetId,
            'related_timesheet_id' => (int)$relatedTimesheetId,
            'direction' => (string)$direction,
            'rest_hours' => (float)$restHours,
            'required_hours' => (float)$requiredHours,
            'message' => (string)$message,
            'createdon' => date('Y-m-d H:i:s'),
            'updatedon' => date('Y-m-d H:i:s'),
        ));
        $violation->save();
    }

    public function rebuildUserViolations($userId)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return 0;
        }

        $this->modx->removeCollection('CrmViolation', array(
            'user_id' => $userId,
        ));

        $assignments = $this->modx->getCollection('CrmAssignment', array(
            'user_id' => $userId,
        ));

        $assignmentIds = array();
        foreach ($assignments as $assignment) {
            $assignmentIds[] = (int)$assignment->get('id');
        }

        if (empty($assignmentIds)) {
            return 0;
        }

        $c = $this->modx->newQuery('CrmTimesheet');
        $c->where(array(
            'assignment_id:IN' => $assignmentIds,
            'status:IN' => array('submitted', 'approved'),
        ));
        $c->sortby('work_date', 'ASC');
        $c->sortby('start_time', 'ASC');
        $c->sortby('id', 'ASC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $c);
        $items = array();

        foreach ($timesheets as $timesheet) {
            $range = $this->buildTimesheetRange($timesheet);
            if ($range) {
                $items[] = $range;
            }
        }

        usort($items, function ($a, $b) {
            if ($a['start_ts'] === $b['start_ts']) {
                return $a['id'] - $b['id'];
            }
            return ($a['start_ts'] < $b['start_ts']) ? -1 : 1;
        });

        $requiredHours = 11.0;
        $createdCount = 0;

        $total = count($items);
        for ($i = 0; $i < $total; $i++) {
            $current = $items[$i];

            if ($i > 0) {
                $prev = $items[$i - 1];
                $gapHours = round(($current['start_ts'] - $prev['end_ts']) / 3600, 2);

                if ($gapHours < $requiredHours) {
                    $message = 'Между предыдущей и текущей записью отдых меньше 11 часов';
                    $this->createViolation(
                        $userId,
                        $current['id'],
                        $prev['id'],
                        'previous',
                        $gapHours,
                        $requiredHours,
                        $message
                    );
                    $createdCount++;
                }
            }

            if ($i < ($total - 1)) {
                $next = $items[$i + 1];
                $gapHours = round(($next['start_ts'] - $current['end_ts']) / 3600, 2);

                if ($gapHours < $requiredHours) {
                    $message = 'Между текущей и следующей записью отдых меньше 11 часов';
                    $this->createViolation(
                        $userId,
                        $current['id'],
                        $next['id'],
                        'next',
                        $gapHours,
                        $requiredHours,
                        $message
                    );
                    $createdCount++;
                }
            }
        }

        return $createdCount;
    }
}