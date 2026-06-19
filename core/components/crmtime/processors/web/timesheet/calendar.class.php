<?php

class CrmTimeWebTimesheetCalendarProcessor extends modProcessor
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
        /** @var modUserProfile $profile */
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

        $seconds = $end - $start;
        return round($seconds / 3600, 2);
    }

    protected function getSignatureUrl($signatureFile)
    {
        $signatureFile = trim((string)$signatureFile);
        if ($signatureFile === '') {
            return '';
        }

        return '/' . ltrim($signatureFile, '/');
    }

    protected function getTariffText(CrmTimesheet $timesheet)
    {
        $flags = array();

        if ((int)$timesheet->get('is_night') === 1) {
            $flags[] = 'Nacht';
        }

        if ((int)$timesheet->get('is_sunday') === 1) {
            $flags[] = 'Sonntag';
        }

        if ((int)$timesheet->get('is_holiday') === 1) {
            $flags[] = 'Feiertag';
        }

        return !empty($flags) ? implode(', ', $flags) : 'Standard';
    }

    public function process()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated('web')) {
            return $this->failure('Требуется авторизация');
        }

        $userId = (int)$this->modx->user->get('id');

        $c = $this->modx->newQuery('CrmTimesheet');
        $c->sortby('work_date', 'ASC');
        $c->sortby('start_time', 'ASC');
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

            if ((int)$assignment->get('user_id') !== $userId) {
                continue;
            }

            $customerId = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');

            /** @var CrmCustomer $customer */
            $customer = $this->modx->getObject('CrmCustomer', array('id' => $customerId));
            /** @var CrmWorkplace $workplace */
            $workplace = $this->modx->getObject('CrmWorkplace', array('id' => $workplaceId));

            $signatureFile = (string)$timesheet->get('signature_file');
            $signatureUrl = $this->getSignatureUrl($signatureFile);
            $hours = $this->getHoursBetween(
                $timesheet->get('start_time'),
                $timesheet->get('end_time')
            );
            $rate = $this->getEffectiveRate($userId, $timesheet);
            $amount = round($hours * $rate, 2);

            $title = ($workplace ? (string)$workplace->get('name') : 'Arbeit');
            $title .= ' / ' . $this->getTariffText($timesheet);

            $rows[] = array(
                'id' => (int)$timesheet->get('id'),
                'title' => $title,
                'start' => $timesheet->get('work_date') . 'T' . $timesheet->get('start_time'),
                'end' => $timesheet->get('work_date') . 'T' . $timesheet->get('end_time'),
                'allDay' => false,
                'extendedProps' => array(
                    'assignment_id' => (int)$timesheet->get('assignment_id'),
                    'customer_name' => $customer ? (string)$customer->get('name') : '',
                    'workplace_name' => $workplace ? (string)$workplace->get('name') : '',
                    'workplace_address' => $workplace ? (string)$workplace->get('address') : '',
                    'work_date' => (string)$timesheet->get('work_date'),
                    'start_time' => (string)$timesheet->get('start_time'),
                    'end_time' => (string)$timesheet->get('end_time'),
                    'status' => (string)$timesheet->get('status'),
                    'admin_comment' => (string)$timesheet->get('admin_comment'),
                    'is_night' => (int)$timesheet->get('is_night'),
                    'is_sunday' => (int)$timesheet->get('is_sunday'),
                    'is_holiday' => (int)$timesheet->get('is_holiday'),
                    'tariff_text' => $this->getTariffText($timesheet),
                    'hours' => number_format($hours, 2, '.', ''),
                    'rate' => number_format($rate, 2, '.', ''),
                    'amount' => number_format($amount, 2, '.', ''),
                    'has_violation' => $this->modx->getCount('CrmViolation', array(
                        'timesheet_id' => (int)$timesheet->get('id'),
                    )) > 0 ? 1 : 0,
                    'createdon' => (string)$timesheet->get('createdon'),
                    'is_signed' => $signatureUrl !== '' ? 1 : 0,
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

return 'CrmTimeWebTimesheetCalendarProcessor';
