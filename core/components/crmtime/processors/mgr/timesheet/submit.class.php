<?php

class CrmTimeMgrTimesheetSubmitProcessor extends modProcessor
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

        $status = (string)$timesheet->get('status');

        if (!in_array($status, array('draft', 'rejected'), true)) {
            return $this->failure('Отправить можно только запись в статусе draft или rejected');
        }

        $timesheet->set('status', 'submitted');
        $timesheet->set('admin_comment', '');
        $timesheet->set('updatedon', date('Y-m-d H:i:s'));

        if (!$timesheet->save()) {
            return $this->failure('Не удалось отправить запись на согласование');
        }

        $userId = $this->modx->crmtime ? $this->modx->crmtime->getTimesheetUserId($timesheet) : 0;
        $violationsCount = 0;

        if ($userId > 0 && $this->modx->crmtime) {
            $violationsCount = $this->modx->crmtime->rebuildUserViolations($userId);
        }

        return $this->success('Запись отправлена на согласование', array(
            'id' => (int)$timesheet->get('id'),
            'status' => $timesheet->get('status'),
            'violations_count' => $violationsCount,
        ));
    }
}

return 'CrmTimeMgrTimesheetSubmitProcessor';