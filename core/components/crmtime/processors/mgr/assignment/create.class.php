<?php

class CrmTimeMgrAssignmentCreateProcessor extends modProcessor
{
    public function process()
    {
        $userId = (int)$this->getProperty('user_id');
        $customerId = (int)$this->getProperty('customer_id');
        $workplaceId = (int)$this->getProperty('workplace_id');
        $startDate = trim((string)$this->getProperty('start_date'));
        $endDate = trim((string)$this->getProperty('end_date'));

        if ($userId <= 0) {
            return $this->failure('Не выбран сотрудник');
        }

        if ($customerId <= 0) {
            return $this->failure('Не выбран заказчик');
        }

        if ($workplaceId <= 0) {
            return $this->failure('Не выбрано место работы');
        }

        $user = $this->modx->getObject('modUser', array(
            'id' => $userId,
        ));
        if (!$user) {
            return $this->failure('Сотрудник не найден');
        }

        $customer = $this->modx->getObject('CrmCustomer', array(
            'id' => $customerId,
        ));
        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        $workplace = $this->modx->getObject('CrmWorkplace', array(
            'id' => $workplaceId,
        ));
        if (!$workplace) {
            return $this->failure('Место работы не найдено');
        }

        if ((int)$workplace->get('customer_id') !== $customerId) {
            return $this->failure('Место работы не принадлежит выбранному заказчику');
        }

        if ($startDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return $this->failure('Дата начала должна быть в формате YYYY-MM-DD');
        }

        if ($endDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            return $this->failure('Дата окончания должна быть в формате YYYY-MM-DD');
        }

        if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
            return $this->failure('Дата окончания не может быть раньше даты начала');
        }

        $assignment = $this->modx->newObject('CrmAssignment');
        $assignment->fromArray(array(
            'user_id' => $userId,
            'customer_id' => $customerId,
            'workplace_id' => $workplaceId,
            'rate' => 0,
            'start_date' => $startDate !== '' ? $startDate : null,
            'end_date' => $endDate !== '' ? $endDate : null,
            'is_active' => 1,
            'createdon' => date('Y-m-d H:i:s'),
            'updatedon' => date('Y-m-d H:i:s'),
        ));

        if (!$assignment->save()) {
            return $this->failure('Не удалось сохранить назначение');
        }

        return $this->success('Назначение создано', array(
            'id' => (int)$assignment->get('id'),
        ));
    }
}

return 'CrmTimeMgrAssignmentCreateProcessor';
