<?php

class CrmTimeMgrReportPdfProcessor extends modProcessor
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

    protected function getRowsData($dateFrom, $dateTo, $customerId, $userIdFilter)
    {
        $c = $this->modx->newQuery('CrmTimesheet');

        if ($dateFrom !== '') {
            $c->where(array('work_date:>=' => $dateFrom));
        }

        if ($dateTo !== '') {
            $c->where(array('work_date:<=' => $dateTo));
        }

        $c->sortby('work_date', 'ASC');
        $c->sortby('id', 'ASC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $c);

        $rows = array();
        $totalHours = 0;
        $totalAmount = 0;

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
            $customerIdRow = (int)$assignment->get('customer_id');
            $workplaceId = (int)$assignment->get('workplace_id');

            if ($customerId > 0 && $customerId !== $customerIdRow) {
                continue;
            }

            if ($userIdFilter > 0 && $userIdFilter !== $userId) {
                continue;
            }

            /** @var modUser $user */
            $user = $this->modx->getObject('modUser', array('id' => $userId));
            /** @var modUserProfile $profile */
            $profile = $this->modx->getObject('modUserProfile', array('internalKey' => $userId));
            /** @var CrmCustomer $customer */
            $customer = $this->modx->getObject('CrmCustomer', array('id' => $customerIdRow));
            /** @var CrmWorkplace $workplace */
            $workplace = $this->modx->getObject('CrmWorkplace', array('id' => $workplaceId));

            $userName = '';
            if ($profile && trim((string)$profile->get('fullname')) !== '') {
                $userName = trim((string)$profile->get('fullname'));
            } elseif ($user) {
                $userName = (string)$user->get('username');
            }

            $hours = $this->getHoursBetween(
                $timesheet->get('start_time'),
                $timesheet->get('end_time')
            );

            $rate = $this->getEffectiveRate($userId, $timesheet);
            $amount = round($hours * $rate, 2);

            $rows[] = array(
                'id' => (int)$timesheet->get('id'),
                'work_date' => (string)$timesheet->get('work_date'),
                'start_time' => (string)$timesheet->get('start_time'),
                'end_time' => (string)$timesheet->get('end_time'),
                'is_night' => (int)$timesheet->get('is_night'),
                'is_sunday' => (int)$timesheet->get('is_sunday'),
                'is_holiday' => (int)$timesheet->get('is_holiday'),
                'user_name' => $userName,
                'customer_name' => $customer ? (string)$customer->get('name') : '',
                'workplace_name' => $workplace ? (string)$workplace->get('name') : '',
                'hours' => number_format($hours, 2, '.', ''),
                'rate' => number_format($rate, 2, '.', ''),
                'amount' => number_format($amount, 2, '.', ''),
            );

            $totalHours += $hours;
            $totalAmount += $amount;
        }

        return array(
            'rows' => $rows,
            'total_hours' => number_format($totalHours, 2, '.', ''),
            'total_amount' => number_format($totalAmount, 2, '.', ''),
        );
    }

    protected function getTariffText($row)
    {
        $flags = array();

        if ((int)$row['is_night'] === 1) {
            $flags[] = 'Ночной';
        }

        if ((int)$row['is_sunday'] === 1) {
            $flags[] = 'Воскресный';
        }

        if ((int)$row['is_holiday'] === 1) {
            $flags[] = 'Праздничный';
        }

        return !empty($flags) ? implode(', ', $flags) : 'Стандарт';
    }

    public function process()
    {
        $dateFrom = trim((string)$this->getProperty('date_from'));
        $dateTo = trim((string)$this->getProperty('date_to'));
        $customerId = (int)$this->getProperty('customer_id');
        $userIdFilter = (int)$this->getProperty('user_id');

        $data = $this->getRowsData($dateFrom, $dateTo, $customerId, $userIdFilter);
        $rows = $data['rows'];

        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
            h1 { font-size: 20px; margin-bottom: 10px; }
            .meta { margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #999; padding: 6px; text-align: left; }
            th { background: #eee; }
            .total { margin-top: 15px; font-weight: bold; }
        </style></head><body>';

        $html .= '<h1>Отчёт по времени</h1>';
        $html .= '<div class="meta">';
        $html .= 'Период: ' . htmlspecialchars(($dateFrom !== '' ? $dateFrom : '...') . ' - ' . ($dateTo !== '' ? $dateTo : '...'), ENT_QUOTES, 'UTF-8');
        $html .= '</div>';

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Время</th>';
        $html .= '<th>Сотрудник</th>';
        $html .= '<th>Заказчик</th>';
        $html .= '<th>Место работы</th>';
        $html .= '<th>Тариф</th>';
        $html .= '<th>Часы</th>';
        $html .= '<th>Ставка</th>';
        $html .= '<th>Сумма</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['work_date'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['start_time'] . ' - ' . $row['end_time'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['workplace_name'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($this->getTariffText($row), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['hours'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['rate'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }

        if (empty($rows)) {
            $html .= '<tr><td colspan="10">Нет данных</td></tr>';
        }

        $html .= '</tbody></table>';

        $html .= '<div class="total">';
        $html .= 'Итого часов: ' . htmlspecialchars($data['total_hours'], ENT_QUOTES, 'UTF-8') . '<br>';
        $html .= 'Итого сумма: ' . htmlspecialchars($data['total_amount'], ENT_QUOTES, 'UTF-8');
        $html .= '</div>';

        $html .= '</body></html>';

        $filename = 'report-' . date('Ymd-His') . '.html';
        $dir = MODX_BASE_PATH . 'assets/components/crmtime/reports/';

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $fullPath = $dir . $filename;
        file_put_contents($fullPath, $html);

        return $this->success('Отчёт сформирован', array(
            'file' => 'assets/components/crmtime/reports/' . $filename,
        ));
    }
}

return 'CrmTimeMgrReportPdfProcessor';