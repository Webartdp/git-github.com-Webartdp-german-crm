<?php

class CrmTimeMgrWorkplaceCreateProcessor extends modProcessor
{
    public function process()
    {
        $customerId = (int)$this->getProperty('customer_id');
        $name = trim((string)$this->getProperty('name'));
        $address = trim((string)$this->getProperty('address'));

        if ($customerId <= 0) {
            return $this->failure('Не выбран заказчик');
        }

        if ($name === '') {
            return $this->failure('Введите название места работы');
        }

        $customer = $this->modx->getObject('CrmCustomer', $customerId);
        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        $workplace = $this->modx->newObject('CrmWorkplace');
        $workplace->fromArray(array(
            'customer_id' => $customerId,
            'name' => $name,
            'address' => $address,
            'is_active' => 1,
            'createdon' => date('Y-m-d H:i:s'),
            'updatedon' => date('Y-m-d H:i:s'),
        ));

        if (!$workplace->save()) {
            return $this->failure('Не удалось сохранить место работы');
        }

        return $this->success('Место работы создано', array(
            'id' => $workplace->get('id'),
            'customer_id' => $workplace->get('customer_id'),
            'name' => $workplace->get('name'),
        ));
    }
}

return 'CrmTimeMgrWorkplaceCreateProcessor';