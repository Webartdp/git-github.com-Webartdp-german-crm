<?php

class CrmTimeMgrWorkplaceGetListProcessor extends modProcessor
{
    public function process()
    {
        $c = $this->modx->newQuery('CrmWorkplace');
        $c->sortby('id', 'DESC');

        $workplaces = $this->modx->getCollection('CrmWorkplace', $c);
        $rows = array();

        foreach ($workplaces as $workplace) {
            $customer = $workplace->getOne('Customer');

            $rows[] = array(
                'id' => $workplace->get('id'),
                'customer_id' => $workplace->get('customer_id'),
                'customer_name' => $customer ? $customer->get('name') : '',
                'name' => $workplace->get('name'),
                'address' => $workplace->get('address'),
                'createdon' => $workplace->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrWorkplaceGetListProcessor';