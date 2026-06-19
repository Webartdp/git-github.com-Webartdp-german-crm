<?php

class CrmTimeWebAssignmentMyListProcessor extends modProcessor
{
    public function process()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return $this->failure('Nicht autorisiert.', array(
                'code' => 401,
            ));
        }

        $userId = (int)$this->modx->user->get('id');
        if ($userId <= 0) {
            return $this->failure('Benutzer nicht gefunden.', array(
                'code' => 401,
            ));
        }

        $c = $this->modx->newQuery('CrmAssignment');
        $c->where(array(
            'user_id' => $userId,
        ));
        $c->sortby('id', 'DESC');

        $assignments = $this->modx->getCollection('CrmAssignment', $c);
        $rows = array();

        /** @var CrmAssignment $assignment */
        foreach ($assignments as $assignment) {
            $customer = $this->modx->getObject('CrmCustomer', array(
                'id' => (int)$assignment->get('customer_id'),
            ));

            $workplace = $this->modx->getObject('CrmWorkplace', array(
                'id' => (int)$assignment->get('workplace_id'),
            ));

            $rows[] = array(
                'id' => (int)$assignment->get('id'),
                'user_id' => (int)$assignment->get('user_id'),
                'customer_id' => (int)$assignment->get('customer_id'),
                'customer_name' => $customer ? (string)$customer->get('name') : '',
                'workplace_id' => (int)$assignment->get('workplace_id'),
                'workplace_name' => $workplace ? (string)$workplace->get('name') : '',
                'workplace_address' => $workplace ? (string)$workplace->get('address') : '',
                'rate' => (string)$assignment->get('rate'),
                'start_date' => (string)$assignment->get('start_date'),
                'end_date' => (string)$assignment->get('end_date'),
                'createdon' => (string)$assignment->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
            'user_id' => $userId,
        ));
    }
}

return 'CrmTimeWebAssignmentMyListProcessor';