<?php

class CrmTimeWebViolationGetListProcessor extends modProcessor
{
    public function initialize()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated('web')) {
            return 'Access denied';
        }

        return parent::initialize();
    }

    public function process()
    {
        $userId = (int)$this->modx->user->get('id');

        $c = $this->modx->newQuery('CrmViolation');
        $c->where(array(
            'user_id' => $userId,
        ));
        $c->sortby('id', 'DESC');

        $violations = $this->modx->getCollection('CrmViolation', $c);
        $rows = array();

        foreach ($violations as $violation) {
            $timesheetId = (int)$violation->get('timesheet_id');
            $relatedTimesheetId = (int)$violation->get('related_timesheet_id');

            $timesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => $timesheetId,
            ));
            $relatedTimesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => $relatedTimesheetId,
            ));

            $rows[] = array(
                'id' => (int)$violation->get('id'),
                'timesheet_id' => $timesheetId,
                'related_timesheet_id' => $relatedTimesheetId,
                'timesheet_date' => $timesheet ? $timesheet->get('work_date') : '',
                'timesheet_start' => $timesheet ? $timesheet->get('start_time') : '',
                'timesheet_end' => $timesheet ? $timesheet->get('end_time') : '',
                'related_date' => $relatedTimesheet ? $relatedTimesheet->get('work_date') : '',
                'related_start' => $relatedTimesheet ? $relatedTimesheet->get('start_time') : '',
                'related_end' => $relatedTimesheet ? $relatedTimesheet->get('end_time') : '',
                'direction' => $violation->get('direction'),
                'rest_hours' => $violation->get('rest_hours'),
                'required_hours' => $violation->get('required_hours'),
                'message' => $violation->get('message'),
                'createdon' => $violation->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeWebViolationGetListProcessor';