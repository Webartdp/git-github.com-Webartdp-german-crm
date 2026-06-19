<?php

class CrmTimeMgrTimesheetRejectProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $comment = trim((string)$this->getProperty('comment'));

        if ($id <= 0) {
            return $this->failure('Не передан ID записи времени');
        }

        if ($comment === '') {
            return $this->failure('Комментарий при отклонении обязателен');
        }

        /** @var CrmTimesheet $timesheet */
        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $id,
        ));

        if (!$timesheet) {
            return $this->failure('Запись времени не найдена');
        }

        if ((string)$timesheet->get('status') !== 'submitted') {
            return $this->failure('Отклонить можно только запись в статусе submitted');
        }

        $timesheet->set('status', 'rejected');
        $timesheet->set('admin_comment', $comment);
        $timesheet->set('updatedon', date('Y-m-d H:i:s'));

        if (!$timesheet->save()) {
            return $this->failure('Не удалось отклонить запись');
        }

        $userId = $this->modx->crmtime ? $this->modx->crmtime->getTimesheetUserId($timesheet) : 0;
        $violationsCount = 0;

        if ($userId > 0 && $this->modx->crmtime) {
            $violationsCount = $this->modx->crmtime->rebuildUserViolations($userId);
        }

        return $this->success('Запись отклонена', array(
            'id' => (int)$timesheet->get('id'),
            'status' => $timesheet->get('status'),
            'comment' => $timesheet->get('admin_comment'),
            'violations_count' => $violationsCount,
        ));
    }
}

return 'CrmTimeMgrTimesheetRejectProcessor';