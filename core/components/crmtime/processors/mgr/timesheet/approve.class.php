<?php

class CrmTimeMgrTimesheetApproveProcessor extends modProcessor
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

        if ((string)$timesheet->get('status') !== 'submitted') {
            return $this->failure('Утвердить можно только запись в статусе submitted');
        }

        $timesheet->set('status', 'approved');
        $timesheet->set('admin_comment', '');
        $timesheet->set('updatedon', date('Y-m-d H:i:s'));

        if (!$timesheet->save()) {
            return $this->failure('Не удалось утвердить запись');
        }

        $userId = $this->modx->crmtime ? $this->modx->crmtime->getTimesheetUserId($timesheet) : 0;
        $violationsCount = 0;

        if ($userId > 0 && $this->modx->crmtime) {
            $violationsCount = $this->modx->crmtime->rebuildUserViolations($userId);
        }

        return $this->success('Запись утверждена', array(
            'id' => (int)$timesheet->get('id'),
            'status' => $timesheet->get('status'),
            'violations_count' => $violationsCount,
        ));
    }
}

return 'CrmTimeMgrTimesheetApproveProcessor';