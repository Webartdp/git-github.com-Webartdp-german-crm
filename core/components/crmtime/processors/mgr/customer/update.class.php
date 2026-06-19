<?php

class CrmTimeMgrCustomerUpdateProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $name = trim((string)$this->getProperty('name'));
        $code = trim((string)$this->getProperty('code'));
        $description = trim((string)$this->getProperty('description'));

        if ($id <= 0) {
            return $this->failure('Не передан ID заказчика');
        }

        if ($name === '') {
            return $this->failure('Укажите название заказчика');
        }

        /** @var CrmCustomer $customer */
        $customer = $this->modx->getObject('CrmCustomer', array(
            'id' => $id,
        ));

        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        $customer->set('name', $name);
        $customer->set('code', $code);
        $customer->set('description', $description);

        if (!$customer->save()) {
            return $this->failure('Не удалось обновить заказчика');
        }

        return $this->success('Заказчик обновлён', array(
            'id' => (int)$customer->get('id'),
        ));
    }
}

return 'CrmTimeMgrCustomerUpdateProcessor';