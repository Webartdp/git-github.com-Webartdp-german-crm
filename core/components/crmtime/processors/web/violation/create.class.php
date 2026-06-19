<?php

class CrmTimeWebTimesheetCreateProcessor extends modProcessor
{
    protected function getCurrentUserId()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return 0;
        }

        return (int)$this->modx->user->get('id');
    }

    protected function getOwnAssignment($assignmentId, $userId)
    {
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => (int)$assignmentId,
        ));

        if (!$assignment) {
            return null;
        }

        if ((int)$assignment->get('user_id') !== (int)$userId) {
            return null;
        }

        return $assignment;
    }

    protected function recalculateViolations($userId)
    {
        $assignmentIds = array();

        $ac = $this->modx->newQuery('CrmAssignment');
        $ac->where(array(
            'user_id' => (int)$userId,
        ));
        $assignments = $this->modx->getCollection('CrmAssignment', $ac);
        /** @var CrmAssignment $assignment */
        foreach ($assignments as $assignment) {
            $assignmentIds[] = (int)$assignment->get('id');
        }

        if (empty($assignmentIds)) {
            return;
        }

        $tc = $this->modx->newQuery('CrmTimesheet');
        $tc->where(array(
            'assignment_id:IN' => $assignmentIds,
        ));
        $tc->sortby('work_date', 'ASC');
        $tc->sortby('start_time', 'ASC');
        $tc->sortby('id', 'ASC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $tc);

        $timesheetIds = array();
        foreach ($timesheets as $timesheet) {
            $timesheetIds[] = (int)$timesheet->get('id');
        }

        if (!empty($timesheetIds)) {
            $vc = $this->modx->newQuery('CrmViolation');
            $vc->where(array(
                'timesheet_id:IN' => $timesheetIds,
            ));
            $violations = $this->modx->getCollection('CrmViolation', $vc);
            /** @var CrmViolation $violation */
            foreach ($violations as $violation) {
                $violation->remove();
            }
        }

        $prev = null;
        /** @var CrmTimesheet $current */
        foreach ($timesheets as $current) {
            if (!$prev) {
                $prev = $current;
                continue;
            }

            $prevEnd = strtotime($prev->get('work_date') . ' ' . $prev->get('end_time'));
            $currStart = strtotime($current->get('work_date') . ' ' . $current->get('start_time'));

            if ($prevEnd && $currStart) {
                $restHours = ($currStart - $prevEnd) / 3600;

                if ($restHours < 11) {
                    $violation = $this->modx->newObject('CrmViolation');
                    if ($violation) {
                        $violation->set('timesheet_id', (int)$current->get('id'));
                        $violation->set('related_timesheet_id', (int)$prev->get('id'));
                        $violation->set('direction', 'previous_to_current');
                        $violation->set('rest_hours', round($restHours, 2));
                        $violation->set('required_hours', 11);
                        $violation->set(
                            'message',
                            'Ruhezeit zwischen Eintrag #' . (int)$prev->get('id') . ' und #' . (int)$current->get('id') . ' beträgt nur ' . round($restHours, 2) . ' Std.'
                        );
                        $violation->save();
                    }
                }
            }

            $prev = $current;
        }
    }

    public function process()
    {
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->failure('Nicht autorisiert.', array(
                'code' => 401,
            ));
        }

        $assignmentId = (int)$this->getProperty('assignment_id');
        $workDate = trim((string)$this->getProperty('work_date'));
        $startTime = trim((string)$this->getProperty('start_time'));
        $endTime = trim((string)$this->getProperty('end_time'));

        if ($assignmentId <= 0) {
            return $this->failure('Einsatz wurde nicht ausgewählt.');
        }

        if ($workDate === '' || $startTime === '' || $endTime === '') {
            return $this->failure('Bitte alle Felder ausfüllen.');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $workDate)) {
            return $this->failure('Ungültiges Datum.');
        }

        if (!preg_match('/^\d{2}:\d{2}/', $startTime) || !preg_match('/^\d{2}:\d{2}/', $endTime)) {
            return $this->failure('Ungültige Uhrzeit.');
        }

        $startTs = strtotime($workDate . ' ' . substr($startTime, 0, 5));
        $endTs = strtotime($workDate . ' ' . substr($endTime, 0, 5));

        if (!$startTs || !$endTs || $endTs <= $startTs) {
            return $this->failure('Die Endzeit muss später als die Startzeit sein.');
        }

        $assignment = $this->getOwnAssignment($assignmentId, $userId);
        if (!$assignment) {
            return $this->failure('Einsatz nicht gefunden oder kein Zugriff.');
        }

        $timesheet = $this->modx->newObject('CrmTimesheet');
        if (!$timesheet) {
            return $this->failure('Zeiteintrag konnte nicht erstellt werden.');
        }

        $timesheet->set('assignment_id', $assignmentId);
        $timesheet->set('work_date', $workDate);
        $timesheet->set('start_time', substr($startTime, 0, 5) . ':00');
        $timesheet->set('end_time', substr($endTime, 0, 5) . ':00');
        $timesheet->set('status', 'draft');
        $timesheet->set('admin_comment', '');

        if (!$timesheet->save()) {
            return $this->failure('Zeiteintrag konnte nicht gespeichert werden.');
        }

        $this->recalculateViolations($userId);

        return $this->success('Zeiteintrag wurde gespeichert.', array(
            'id' => (int)$timesheet->get('id'),
            'status' => 'draft',
        ));
    }
}

return 'CrmTimeWebTimesheetCreateProcessor';