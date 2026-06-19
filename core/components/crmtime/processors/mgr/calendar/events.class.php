<?php

class CrmTimeMgrCalendarEventsProcessor extends modProcessor
{
    protected function getExtendedArray(modUserProfile $profile = null)
    {
        if (!$profile) {
            return array();
        }

        $extended = $profile->get('extended');

        if (is_array($extended)) {
            return $extended;
        }

        if (is_string($extended) && $extended !== '') {
            $decoded = json_decode($extended, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return array();
    }

    protected function getStatusPrefix($status)
    {
        switch ($status) {
            case 'submitted':
                return '⏳ ';
            case 'approved':
                return '✓ ';
            case 'rejected':
                return '✕ ';
            case 'draft':
            default:
                return '• ';
        }
    }

    protected function getStatusFallbackColor($status, $hasViolation)
    {
        if ($hasViolation) {
            return '#fd7e14';
        }

        switch ($status) {
            case 'submitted':
                return '#0d6efd';
            case 'approved':
                return '#198754';
            case 'rejected':
                return '#dc3545';
            case 'draft':
            default:
                return '#6c757d';
        }
    }

    protected function getUserCalendarColor(modUserProfile $profile = null, $status = 'draft', $hasViolation = false)
    {
        if ($hasViolation) {
            return '#fd7e14';
        }

        $extended = $this->getExtendedArray($profile);

        if (!empty($extended['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $extended['color'])) {
            return $extended['color'];
        }

        return $this->getStatusFallbackColor($status, $hasViolation);
    }

    public function process()
    {
        $start = substr(trim((string)$this->getProperty('start')), 0, 10);
        $end = substr(trim((string)$this->getProperty('end')), 0, 10);

        $filterUserId = (int)$this->getProperty('user_id');
        $filterCustomerId = (int)$this->getProperty('customer_id');
        $filterWorkplaceId = (int)$this->getProperty('workplace_id');
        $filterStatus = trim((string)$this->getProperty('status'));

        $c = $this->modx->newQuery('CrmTimesheet');

        if ($start !== '') {
            $c->where(array('work_date:>=' => $start));
        }
        if ($end !== '') {
            $c->where(array('work_date:<' => $end));
        }

        $c->sortby('work_date', 'ASC');
        $c->sortby('start_time', 'ASC');
        $c->sortby('id', 'ASC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $c);
        $rows = array();

        foreach ($timesheets as $timesheet) {
            /** @var CrmTimesheet $timesheet */
            $assignment = $this->modx->getObject('CrmAssignment', array(
                'id' => (int)$timesheet->get('assignment_id'),
            ));

            if (!$assignment) {
                continue;
            }

            $userId = (int)$assignment->get('user_id');
            $customerId = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');
            $status = (string)$timesheet->get('status');

            if ($filterUserId > 0 && $filterUserId !== $userId) {
                continue;
            }

            if ($filterCustomerId > 0 && $filterCustomerId !== $customerId) {
                continue;
            }

            if ($filterWorkplaceId > 0 && $filterWorkplaceId !== $workplaceId) {
                continue;
            }

            if ($filterStatus !== '' && $filterStatus !== $status) {
                continue;
            }

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

            $userName = '';
            if ($profile && trim((string)$profile->get('fullname')) !== '') {
                $userName = trim((string)$profile->get('fullname'));
            } elseif ($user) {
                $userName = (string)$user->get('username');
            }

            $title = $this->getStatusPrefix($status) . $userName;
            if ((int)$timesheet->get('is_signed') === 1) {
                $title .= ' ✍';
            }
            if ($workplace) {
                $title .= ($title !== '' ? ' / ' : '') . $workplace->get('name');
            } elseif ($customer) {
                $title .= ($title !== '' ? ' / ' : '') . $customer->get('name');
            }

            if ($title === '') {
                $title = 'Запись #' . (int)$timesheet->get('id');
            }

            $hasViolation = $this->modx->getCount('CrmViolation', array(
                'timesheet_id' => (int)$timesheet->get('id'),
            )) > 0 ? 1 : 0;

            $calendarColor = $this->getUserCalendarColor($profile, $status, $hasViolation);

            $rows[] = array(
                'id' => (int)$timesheet->get('id'),
                'title' => $title,
                'start' => $timesheet->get('work_date') . 'T' . $timesheet->get('start_time'),
                'end' => $timesheet->get('work_date') . 'T' . $timesheet->get('end_time'),
                'allDay' => false,
                'backgroundColor' => $calendarColor,
                'borderColor' => $calendarColor,
                'textColor' => '#ffffff',
                'extendedProps' => array(
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'customer_id' => $customerId,
                    'customer_name' => $customer ? $customer->get('name') : '',
                    'workplace_id' => $workplaceId,
                    'workplace_name' => $workplace ? $workplace->get('name') : '',
                    'status' => $status,
                    'has_violation' => $hasViolation,
                    'admin_comment' => $timesheet->get('admin_comment'),
                    'work_date' => $timesheet->get('work_date'),
                    'start_time' => $timesheet->get('start_time'),
                    'end_time' => $timesheet->get('end_time'),
                    'color' => $calendarColor,
                    'is_signed' => (int)$timesheet->get('is_signed'),
                    'signed_on' => (string)$timesheet->get('signed_on'),
                    'signed_name' => (string)$timesheet->get('signed_name'),
                    'signature_url' => trim((string)$timesheet->get('signature_file')) !== '' ? '/' . ltrim((string)$timesheet->get('signature_file'), '/') : '',
                ),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrCalendarEventsProcessor';