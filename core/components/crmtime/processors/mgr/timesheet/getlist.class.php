<?php

class CrmTimeMgrTimesheetGetListProcessor extends modProcessor
{
    protected function getSignatureUrl($signatureFile)
    {
        $signatureFile = trim((string)$signatureFile);
        if ($signatureFile === '') {
            return '';
        }

        return '/' . ltrim($signatureFile, '/');
    }

    public function process()
    {
        $c = $this->modx->newQuery('CrmTimesheet');
        $c->sortby('work_date', 'DESC');
        $c->sortby('id', 'DESC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $c);
        $rows = array();

        foreach ($timesheets as $timesheet) {
            /** @var CrmTimesheet $timesheet */
            /** @var CrmAssignment $assignment */
            $assignment = $this->modx->getObject('CrmAssignment', array(
                'id' => (int)$timesheet->get('assignment_id'),
            ));

            if (!$assignment) {
                continue;
            }

            $userId = (int)$assignment->get('user_id');
            $customerId = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');

            /** @var modUser $user */
            $user = $this->modx->getObject('modUser', array('id' => $userId));
            /** @var modUserProfile $profile */
            $profile = $this->modx->getObject('modUserProfile', array('internalKey' => $userId));
            /** @var CrmCustomer $customer */
            $customer = $this->modx->getObject('CrmCustomer', array('id' => $customerId));
            /** @var CrmWorkplace $workplace */
            $workplace = $this->modx->getObject('CrmWorkplace', array('id' => $workplaceId));

            $signatureFile = (string)$timesheet->get('signature_file');
            $signatureUrl = $this->getSignatureUrl($signatureFile);

            $rows[] = array(
                'id' => (int)$timesheet->get('id'),
                'assignment_id' => (int)$timesheet->get('assignment_id'),
                'work_date' => (string)$timesheet->get('work_date'),
                'start_time' => (string)$timesheet->get('start_time'),
                'end_time' => (string)$timesheet->get('end_time'),
                'is_night' => (int)$timesheet->get('is_night'),
                'is_sunday' => (int)$timesheet->get('is_sunday'),
                'is_holiday' => (int)$timesheet->get('is_holiday'),
                'status' => (string)$timesheet->get('status'),
                'admin_comment' => (string)$timesheet->get('admin_comment'),
                'createdon' => (string)$timesheet->get('createdon'),

                'user_id' => $userId,
                'username' => $user ? (string)$user->get('username') : '',
                'fullname' => $profile ? (string)$profile->get('fullname') : '',

                'customer_id' => $customerId,
                'customer_name' => $customer ? (string)$customer->get('name') : '',

                'workplace_id' => $workplaceId,
                'workplace_name' => $workplace ? (string)$workplace->get('name') : '',
                'workplace_address' => $workplace ? (string)$workplace->get('address') : '',

                'has_violation' => $this->modx->getCount('CrmViolation', array(
                    'timesheet_id' => (int)$timesheet->get('id'),
                )) > 0 ? 1 : 0,

                'signature_type' => (string)$timesheet->get('signature_type'),
                'signature_file' => $signatureFile,
                'signature_url' => $signatureUrl,
                'signed_on' => (string)$timesheet->get('signed_on'),
                'signed_name' => (string)$timesheet->get('signed_name'),
                'is_signed' => $signatureUrl !== '' ? 1 : 0,
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrTimesheetGetListProcessor';