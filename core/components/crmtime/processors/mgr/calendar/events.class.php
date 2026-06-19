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

    protected function getUserTariffs($userId)
    {
        $profile = $this->modx->getObject('modUserProfile', array(
            'internalKey' => (int)$userId,
        ));

        $extended = $this->getExtendedArray($profile);

        $standardRate = isset($extended['standard_rate']) ? str_replace(',', '.', trim((string)$extended['standard_rate'])) : '0';
        $nightCoeff = isset($extended['night_coeff']) ? str_replace(',', '.', trim((string)$extended['night_coeff'])) : '1';
        $sundayCoeff = isset($extended['sunday_coeff']) ? str_replace(',', '.', trim((string)$extended['sunday_coeff'])) : '1';
        $holidayCoeff = isset($extended['holiday_coeff']) ? str_replace(',', '.', trim((string)$extended['holiday_coeff'])) : '1';

        return array(
            'standard_rate' => is_numeric($standardRate) ? (float)$standardRate : 0,
            'night_coeff' => is_numeric($nightCoeff) && (float)$nightCoeff > 0 ? (float)$nightCoeff : 1,
            'sunday_coeff' => is_numeric($sundayCoeff) && (float)$sundayCoeff > 0 ? (float)$sundayCoeff : 1,
            'holiday_coeff' => is_numeric($holidayCoeff) && (float)$holidayCoeff > 0 ? (float)$holidayCoeff : 1,
        );
    }

    protected function getEffectiveRate($userId, CrmTimesheet $timesheet)
    {
        $tariffs = $this->getUserTariffs($userId);

        $rate = (float)$tariffs['standard_rate'];

        if ((int)$timesheet->get('is_night') === 1) {
            $rate = $rate * (float)$tariffs['night_coeff'];
        }

        if ((int)$timesheet->get('is_sunday') === 1) {
            $rate = $rate * (float)$tariffs['sunday_coeff'];
        }

        if ((int)$timesheet->get('is_holiday') === 1) {
            $rate = $rate * (float)$tariffs['holiday_coeff'];
        }

        return round($rate, 2);
    }

    protected function getHoursBetween($startTime, $endTime)
    {
        $startTime = trim((string)$startTime);
        $endTime = trim((string)$endTime);

        if ($startTime === '' || $endTime === '') {
            return 0;
        }

        $start = strtotime('1970-01-01 ' . $startTime);
        $end = strtotime('1970-01-01 ' . $endTime);

        if ($start === false || $end === false) {
            return 0;
        }

        if ($end < $start) {
            $end += 86400;
        }

        return round(($end - $start) / 3600, 2);
    }

    protected function getTariffText(CrmTimesheet $timesheet)
    {
        $flags = array();

        if ((int)$timesheet->get('is_night') === 1) {
            $flags[] = 'Ночь';
        }
        if ((int)$timesheet->get('is_sunday') === 1) {
            $flags[] = 'Воскресенье';
        }
        if ((int)$timesheet->get('is_holiday') === 1) {
            $flags[] = 'Праздник';
        }

        return $flags ? implode(', ', $flags) : 'Стандарт';
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

            $signatureFile = (string)$timesheet->get('signature_file');
            $signatureUrl = $this->getSignatureUrl($signatureFile);
            $hasSignature = $signatureUrl !== '' ? 1 : 0;

            $tariffText = $this->getTariffText($timesheet);
            $hours = $this->getHoursBetween(
                $timesheet->get('start_time'),
                $timesheet->get('end_time')
            );
            $rate = $this->getEffectiveRate($userId, $timesheet);
            $amount = round($hours * $rate, 2);

            $title = $this->getStatusPrefix($status) . $userName;
            if ($hasSignature) {
                $title .= ' ✍';
            }
            if ($workplace) {
                $title .= ($title !== '' ? ' / ' : '') . $workplace->get('name');
            } elseif ($customer) {
                $title .= ($title !== '' ? ' / ' : '') . $customer->get('name');
            }
            $title .= ($title !== '' ? ' / ' : '') . $tariffText;

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
                    'workplace_address' => $workplace ? $workplace->get('address') : '',
                    'status' => $status,
                    'has_violation' => $hasViolation,
                    'admin_comment' => $timesheet->get('admin_comment'),
                    'work_date' => $timesheet->get('work_date'),
                    'start_time' => $timesheet->get('start_time'),
                    'end_time' => $timesheet->get('end_time'),
                    'is_night' => (int)$timesheet->get('is_night'),
                    'is_sunday' => (int)$timesheet->get('is_sunday'),
                    'is_holiday' => (int)$timesheet->get('is_holiday'),
                    'tariff_text' => $tariffText,
                    'hours' => number_format($hours, 2, '.', ''),
                    'rate' => number_format($rate, 2, '.', ''),
                    'amount' => number_format($amount, 2, '.', ''),
                    'color' => $calendarColor,
                    'createdon' => (string)$timesheet->get('createdon'),
                    'is_signed' => $hasSignature,
                    'signed_on' => (string)$timesheet->get('signed_on'),
                    'signed_name' => (string)$timesheet->get('signed_name'),
                    'signature_url' => $signatureUrl,
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
