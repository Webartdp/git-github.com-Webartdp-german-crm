<?php

class CrmTimeMgrWorkplaceUpdateProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $customerId = (int)$this->getProperty('customer_id');
        $name = trim((string)$this->getProperty('name'));
        $address = trim((string)$this->getProperty('address'));

        if ($id <= 0) {
            return $this->failure('Не передан ID места работы');
        }

        if ($customerId <= 0) {
            return $this->failure('Не выбран заказчик');
        }

        if ($name === '') {
            return $this->failure('Укажите название места работы');
        }

        $customer = $this->modx->getObject('CrmCustomer', array(
            'id' => $customerId,
        ));

        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        /** @var CrmWorkplace $workplace */
        $workplace = $this->modx->getObject('CrmWorkplace', array(
            'id' => $id,
        ));

        if (!$workplace) {
            return $this->failure('Место работы не найдено');
        }

        $workplace->set('customer_id', $customerId);
        $workplace->set('name', $name);
        $workplace->set('address', $address);

        if (!$workplace->save()) {
            return $this->failure('Не удалось обновить место работы');
        }

        return $this->success('Место работы обновлено', array(
            'id' => (int)$workplace->get('id'),
        ));
    }
}

return 'CrmTimeMgrWorkplaceUpdateProcessor';