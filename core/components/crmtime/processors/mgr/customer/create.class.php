<?php

class CrmTimeMgrCustomerCreateProcessor extends modProcessor
{
    public function process()
    {
        $name = trim((string)$this->getProperty('name'));
        $code = trim((string)$this->getProperty('code'));
        $description = trim((string)$this->getProperty('description'));

        if ($name === '') {
            return $this->failure('Введите название заказчика');
        }

        $exists = $this->modx->getObject('CrmCustomer', array(
            'name' => $name,
        ));

        if ($exists) {
            return $this->failure('Заказчик с таким названием уже существует');
        }

        /** @var CrmCustomer $customer */
        $customer = $this->modx->newObject('CrmCustomer');
        $customer->fromArray(array(
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'is_active' => 1,
            'createdon' => date('Y-m-d H:i:s'),
            'updatedon' => date('Y-m-d H:i:s'),
        ));

        if (!$customer->save()) {
            return $this->failure('Не удалось сохранить заказчика');
        }

        return $this->success('Заказчик создан', array(
            'id' => $customer->get('id'),
            'name' => $customer->get('name'),
            'code' => $customer->get('code'),
        ));
    }
}

return 'CrmTimeMgrCustomerCreateProcessor';