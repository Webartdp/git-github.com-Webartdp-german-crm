<?php

class CrmTimeMgrWorkplaceRemoveProcessor extends modProcessor
{
    protected function removeTimesheetCascade($timesheetId)
    {
        $timesheetId = (int)$timesheetId;
        if ($timesheetId <= 0) {
            return;
        }

        $this->modx->removeCollection('CrmViolation', array(
            'timesheet_id' => $timesheetId,
        ));
        $this->modx->removeCollection('CrmViolation', array(
            'related_timesheet_id' => $timesheetId,
        ));

        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $timesheetId,
        ));

        if ($timesheet) {
            $timesheet->remove();
        }
    }

    protected function removeAssignmentCascade(CrmAssignment $assignment)
    {
        $assignmentId = (int)$assignment->get('id');

        $timesheets = $this->modx->getCollection('CrmTimesheet', array(
            'assignment_id' => $assignmentId,
        ));

        foreach ($timesheets as $timesheet) {
            $this->removeTimesheetCascade($timesheet->get('id'));
        }

        $assignment->remove();
    }

    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не передан ID места работы');
        }

        /** @var CrmWorkplace $workplace */
        $workplace = $this->modx->getObject('CrmWorkplace', array(
            'id' => $id,
        ));

        if (!$workplace) {
            return $this->failure('Место работы не найдено');
        }

        $assignments = $this->modx->getCollection('CrmAssignment', array(
            'workplace_id' => $id,
        ));

        $affectedUsers = array();

        foreach ($assignments as $assignment) {
            $affectedUsers[(int)$assignment->get('user_id')] = (int)$assignment->get('user_id');
            $this->removeAssignmentCascade($assignment);
        }

        if (!$workplace->remove()) {
            return $this->failure('Не удалось удалить место работы');
        }

        if ($this->modx->crmtime) {
            foreach ($affectedUsers as $userId) {
                if ($userId > 0) {
                    $this->modx->crmtime->rebuildUserViolations($userId);
                }
            }
        }

        return $this->success('Место работы удалено', array(
            'id' => $id,
        ));
    }
}

return 'CrmTimeMgrWorkplaceRemoveProcessor';