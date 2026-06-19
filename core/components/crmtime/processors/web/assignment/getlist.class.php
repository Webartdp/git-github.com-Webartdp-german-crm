<?php

class CrmTimeWebAssignmentGetListProcessor extends modProcessor
{
    public function initialize()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated('web')) {
            return 'Access denied';
        }

        return parent::initialize();
    }

    public function process()
    {
        $userId = (int)$this->modx->user->get('id');

        $c = $this->modx->newQuery('CrmAssignment');
        $c->where(array(
            'user_id' => $userId,
            'is_active' => 1,
        ));
        $c->sortby('id', 'DESC');

        $assignments = $this->modx->getCollection('CrmAssignment', $c);
        $rows = array();

        foreach ($assignments as $assignment) {
            $customerId = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');

            $customer = $this->modx->getObject('CrmCustomer', array(
                'id' => $customerId,
            ));
            $workplace = $this->modx->getObject('CrmWorkplace', array(
                'id' => $workplaceId,
            ));

            $rows[] = array(
                'id' => (int)$assignment->get('id'),
                'customer_id' => $customerId,
                'customer_name' => $customer ? $customer->get('name') : '',
                'workplace_id' => $workplaceId,
                'workplace_name' => $workplace ? $workplace->get('name') : '',
                'rate' => $assignment->get('rate'),
                'start_date' => $assignment->get('start_date'),
                'end_date' => $assignment->get('end_date'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeWebAssignmentGetListProcessor';