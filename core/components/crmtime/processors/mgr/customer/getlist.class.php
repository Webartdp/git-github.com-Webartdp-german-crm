<?php

class CrmTimeMgrCustomerGetListProcessor extends modProcessor
{
    public function process()
    {
        $c = $this->modx->newQuery('CrmCustomer');
        $c->sortby('id', 'DESC');

        $customers = $this->modx->getCollection('CrmCustomer', $c);
        $rows = array();

        /** @var CrmCustomer $customer */
        foreach ($customers as $customer) {
            $rows[] = array(
                'id' => $customer->get('id'),
                'name' => $customer->get('name'),
                'code' => $customer->get('code'),
                'description' => $customer->get('description'),
                'is_active' => (int)$customer->get('is_active'),
                'createdon' => $customer->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrCustomerGetListProcessor';