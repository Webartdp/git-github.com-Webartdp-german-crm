<?php

class CrmTimeMgrAssignmentRemoveProcessor extends modProcessor
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

    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не передан ID назначения');
        }

        /** @var CrmAssignment $assignment */
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $id,
        ));

        if (!$assignment) {
            return $this->failure('Назначение не найдено');
        }

        $userId = (int)$assignment->get('user_id');

        $timesheets = $this->modx->getCollection('CrmTimesheet', array(
            'assignment_id' => $id,
        ));

        foreach ($timesheets as $timesheet) {
            $this->removeTimesheetCascade($timesheet->get('id'));
        }

        if (!$assignment->remove()) {
            return $this->failure('Не удалось удалить назначение');
        }

        if ($this->modx->crmtime && $userId > 0) {
            $this->modx->crmtime->rebuildUserViolations($userId);
        }

        return $this->success('Назначение удалено', array(
            'id' => $id,
        ));
    }
}

return 'CrmTimeMgrAssignmentRemoveProcessor';