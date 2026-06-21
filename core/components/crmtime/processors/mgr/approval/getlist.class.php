<?php

class CrmTimeMgrApprovalGetListProcessor extends modProcessor
{
    protected function getSignatureUrl($signatureFile)
    {
        $signatureFile = trim((string)$signatureFile);
        if ($signatureFile === '') {
            return '';
        }

        return '/' . ltrim($signatureFile, '/');
    }

    protected function hasSignature(CrmTimesheet $timesheet, $signatureUrl)
    {
        if (trim((string)$signatureUrl) !== '') {
            return 1;
        }

        if (trim((string)$timesheet->get('signature_file')) !== '') {
            return 1;
        }

        if (trim((string)$timesheet->get('signed_on')) !== '') {
            return 1;
        }

        if (trim((string)$timesheet->get('signed_name')) !== '') {
            return 1;
        }

        if ((int)$timesheet->get('is_signed') === 1) {
            return 1;
        }

        return 0;
    }

    public function process()
    {
        $c = $this->modx->newQuery('CrmTimesheet');
        $c->where(array(
            'status' => 'submitted',
        ));
        $c->sortby('work_date', 'ASC');
        $c->sortby('id', 'ASC');

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
            $hasSignature = $this->hasSignature($timesheet, $signatureUrl);

            $userName = '';
            if ($profile && trim((string)$profile->get('fullname')) !== '') {
                $userName = trim((string)$profile->get('fullname'));
            } elseif ($user) {
                $userName = (string)$user->get('username');
            }

            $rows[] = array(
                'id' => (int)$timesheet->get('id'),
                'work_date' => (string)$timesheet->get('work_date'),
                'start_time' => (string)$timesheet->get('start_time'),
                'end_time' => (string)$timesheet->get('end_time'),
                'status' => (string)$timesheet->get('status'),
                'admin_comment' => (string)$timesheet->get('admin_comment'),
                'is_night' => (int)$timesheet->get('is_night'),
                'is_sunday' => (int)$timesheet->get('is_sunday'),
                'is_holiday' => (int)$timesheet->get('is_holiday'),

                'user_id' => $userId,
                'user_name' => $userName,

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
                'is_signed' => $hasSignature,
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrApprovalGetListProcessor';
