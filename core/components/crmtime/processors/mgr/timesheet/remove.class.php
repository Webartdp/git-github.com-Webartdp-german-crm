<?php

class CrmTimeMgrTimesheetRemoveProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не передан ID записи времени');
        }

        /** @var CrmTimesheet $timesheet */
        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $id,
        ));

        if (!$timesheet) {
            return $this->failure('Запись времени не найдена');
        }

        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => (int)$timesheet->get('assignment_id'),
        ));

        $userId = $assignment ? (int)$assignment->get('user_id') : 0;

        $this->modx->removeCollection('CrmViolation', array(
            'timesheet_id' => $id,
        ));
        $this->modx->removeCollection('CrmViolation', array(
            'related_timesheet_id' => $id,
        ));

        if (!$timesheet->remove()) {
            return $this->failure('Не удалось удалить запись времени');
        }

        if ($this->modx->crmtime && $userId > 0) {
            $this->modx->crmtime->rebuildUserViolations($userId);
        }

        return $this->success('Запись времени удалена', array(
            'id' => $id,
        ));
    }
}

return 'CrmTimeMgrTimesheetRemoveProcessor';