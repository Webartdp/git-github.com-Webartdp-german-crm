<?php

class CrmTimeMgrAssignmentGetListProcessor extends modProcessor
{
    public function process()
    {
        $c = $this->modx->newQuery('CrmAssignment');
        $c->sortby('id', 'DESC');

        $assignments = $this->modx->getCollection('CrmAssignment', $c);
        $rows = array();

        foreach ($assignments as $assignment) {
            $userId = (int)$assignment->get('user_id');
            $customerId = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');

            $user = $this->modx->getObject('modUser', array(
                'id' => $userId,
            ));
            $profile = $this->modx->getObject('modUserProfile', array(
                'internalKey' => $userId,
            ));
            $customer = $this->modx->getObject('CrmCustomer', array(
                'id' => $customerId,
            ));
            $workplace = $this->modx->getObject('CrmWorkplace', array(
                'id' => $workplaceId,
            ));

            $rows[] = array(
                'id' => (int)$assignment->get('id'),
                'user_id' => $userId,
                'username' => $user ? $user->get('username') : '',
                'fullname' => $profile ? $profile->get('fullname') : '',
                'customer_id' => $customerId,
                'customer_name' => $customer ? $customer->get('name') : '',
                'workplace_id' => $workplaceId,
                'workplace_name' => $workplace ? $workplace->get('name') : '',
                'rate' => '',
                'start_date' => $assignment->get('start_date'),
                'end_date' => $assignment->get('end_date'),
                'createdon' => $assignment->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrAssignmentGetListProcessor';
