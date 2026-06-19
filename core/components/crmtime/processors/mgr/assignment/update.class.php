<?php

class CrmTimeMgrAssignmentUpdateProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $userId = (int)$this->getProperty('user_id');
        $customerId = (int)$this->getProperty('customer_id');
        $workplaceId = (int)$this->getProperty('workplace_id');
        $rate = trim((string)$this->getProperty('rate'));
        $startDate = trim((string)$this->getProperty('start_date'));
        $endDate = trim((string)$this->getProperty('end_date'));

        if ($id <= 0) {
            return $this->failure('Не передан ID назначения');
        }

        if ($userId <= 0) {
            return $this->failure('Не выбран сотрудник');
        }

        if ($customerId <= 0) {
            return $this->failure('Не выбран заказчик');
        }

        if ($workplaceId <= 0) {
            return $this->failure('Не выбрано место работы');
        }

        if ($rate === '') {
            return $this->failure('Укажите ставку');
        }

        if ($startDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return $this->failure('Дата начала должна быть в формате YYYY-MM-DD');
        }

        if ($endDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            return $this->failure('Дата окончания должна быть в формате YYYY-MM-DD');
        }

        $user = $this->modx->getObject('modUser', array('id' => $userId));
        if (!$user) {
            return $this->failure('Сотрудник не найден');
        }

        $customer = $this->modx->getObject('CrmCustomer', array('id' => $customerId));
        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        $workplace = $this->modx->getObject('CrmWorkplace', array('id' => $workplaceId));
        if (!$workplace) {
            return $this->failure('Место работы не найдено');
        }

        /** @var CrmAssignment $assignment */
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $id,
        ));

        if (!$assignment) {
            return $this->failure('Назначение не найдено');
        }

        $oldUserId = (int)$assignment->get('user_id');

        $assignment->set('user_id', $userId);
        $assignment->set('customer_id', $customerId);
        $assignment->set('workplace_id', $workplaceId);
        $assignment->set('rate', $rate);
        $assignment->set('start_date', $startDate);
        $assignment->set('end_date', $endDate);

        if (!$assignment->save()) {
            return $this->failure('Не удалось обновить назначение');
        }

        if ($this->modx->crmtime) {
            if ($oldUserId > 0) {
                $this->modx->crmtime->rebuildUserViolations($oldUserId);
            }
            if ($userId > 0 && $userId !== $oldUserId) {
                $this->modx->crmtime->rebuildUserViolations($userId);
            }
        }

        return $this->success('Назначение обновлено', array(
            'id' => (int)$assignment->get('id'),
        ));
    }
}

return 'CrmTimeMgrAssignmentUpdateProcessor';