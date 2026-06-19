<?php

class CrmTimeMgrReportCreatePdfProcessor extends modProcessor
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

    protected function getDocumentsTable()
    {
        return $this->modx->getOption('table_prefix') . 'crm_documents';
    }

    protected function ensureDocumentsTable()
    {
        $table = $this->getDocumentsTable();

        $sql = "
            CREATE TABLE IF NOT EXISTS `{$table}` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `type` VARCHAR(50) NOT NULL DEFAULT 'report',
                `title` VARCHAR(255) NOT NULL DEFAULT '',
                `file_name` VARCHAR(255) NOT NULL DEFAULT '',
                `file_path` VARCHAR(500) NOT NULL DEFAULT '',
                `mime` VARCHAR(100) NOT NULL DEFAULT 'application/pdf',
                `extension` VARCHAR(20) NOT NULL DEFAULT 'pdf',
                `date_from` DATE NULL DEFAULT NULL,
                `date_to` DATE NULL DEFAULT NULL,
                `customer_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
                `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `user_name` VARCHAR(255) NOT NULL DEFAULT '',
                `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
                `createdby_name` VARCHAR(255) NOT NULL DEFAULT '',
                `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `meta` MEDIUMTEXT NULL,
                `createdon` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        return $this->modx->exec($sql) !== false;
    }

    protected function escape($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    protected function buildTariffFlagsText(array $row)
    {
        $flags = array();

        if (!empty($row['is_night'])) {
            $flags[] = 'Ночь';
        }
        if (!empty($row['is_sunday'])) {
            $flags[] = 'Воскресенье';
        }
        if (!empty($row['is_holiday'])) {
            $flags[] = 'Праздник';
        }

        return $flags ? implode(', ', $flags) : 'Стандарт';
    }

    protected function collectReportData($dateFrom, $dateTo, $customerId, $userIdFilter)
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
        $employees = array();
        $totalRecords = 0;
        $totalHours = 0;
        $totalAmount = 0;
        $customerNameFilter = '';
        $userNameFilter = '';

        foreach ($timesheets as $timesheet) {
            /** @var CrmTimesheet $timesheet */
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

            $user = $this->modx->getObject('modUser', array('id' => $userId));
            $profile = $this->modx->getObject('modUserProfile', array('internalKey' => $userId));
            $customer = $this->modx->getObject('CrmCustomer', array('id' => $customerIdRow));
            $workplace = $this->modx->getObject('CrmWorkplace', array('id' => $workplaceId));

            $userName = '';
            if ($profile && trim((string)$profile->get('fullname')) !== '') {
                $userName = trim((string)$profile->get('fullname'));
            } elseif ($user) {
                $userName = (string)$user->get('username');
            }

            if ($customerId > 0 && $customer && $customerNameFilter === '') {
                $customerNameFilter = (string)$customer->get('name');
            }
            if ($userIdFilter > 0 && $userNameFilter === '') {
                $userNameFilter = $userName;
            }

            $hours = $this->getHoursBetween($timesheet->get('start_time'), $timesheet->get('end_time'));
            $rate = $this->getEffectiveRate($userId, $timesheet);
            $amount = round($hours * $rate, 2);

            $row = array(
                'id' => (int)$timesheet->get('id'),
                'work_date' => (string)$timesheet->get('work_date'),
                'start_time' => (string)$timesheet->get('start_time'),
                'end_time' => (string)$timesheet->get('end_time'),
                'is_night' => (int)$timesheet->get('is_night'),
                'is_sunday' => (int)$timesheet->get('is_sunday'),
                'is_holiday' => (int)$timesheet->get('is_holiday'),
                'user_id' => $userId,
                'user_name' => $userName,
                'customer_id' => $customerIdRow,
                'customer_name' => $customer ? (string)$customer->get('name') : '',
                'workplace_id' => $workplaceId,
                'workplace_name' => $workplace ? (string)$workplace->get('name') : '',
                'hours' => number_format($hours, 2, '.', ''),
                'rate' => number_format($rate, 2, '.', ''),
                'amount' => number_format($amount, 2, '.', ''),
            );

            $rows[] = $row;

            if (!isset($employees[$userId])) {
                $employees[$userId] = array(
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'records' => 0,
                    'hours' => 0,
                    'amount' => 0,
                );
            }

            $employees[$userId]['records']++;
            $employees[$userId]['hours'] += $hours;
            $employees[$userId]['amount'] += $amount;

            $totalRecords++;
            $totalHours += $hours;
            $totalAmount += $amount;
        }

        $employeeRows = array();
        foreach ($employees as $employee) {
            $employeeRows[] = array(
                'user_id' => $employee['user_id'],
                'user_name' => $employee['user_name'],
                'records' => $employee['records'],
                'hours' => number_format($employee['hours'], 2, '.', ''),
                'amount' => number_format($employee['amount'], 2, '.', ''),
            );
        }

        return array(
            'summary' => array(
                'records' => $totalRecords,
                'hours' => number_format($totalHours, 2, '.', ''),
                'amount' => number_format($totalAmount, 2, '.', ''),
            ),
            'employees' => $employeeRows,
            'rows' => $rows,
            'customer_name_filter' => $customerNameFilter,
            'user_name_filter' => $userNameFilter,
        );
    }

    protected function buildHtml($report, $dateFrom, $dateTo)
    {
        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'body{font-family:DejaVu Sans, sans-serif;font-size:12px;color:#111;}';
        $html .= 'h1,h2,h3{margin:0 0 10px;}';
        $html .= '.meta{margin-bottom:15px;}';
        $html .= 'table{width:100%;border-collapse:collapse;margin-bottom:18px;}';
        $html .= 'th,td{border:1px solid #999;padding:6px;vertical-align:top;}';
        $html .= 'th{background:#efefef;}';
        $html .= '</style></head><body>';

        $html .= '<h1>crmTime — Отчёт</h1>';
        $html .= '<div class="meta">';
        $html .= '<div><strong>Дата с:</strong> ' . $this->escape($dateFrom !== '' ? $dateFrom : '—') . '</div>';
        $html .= '<div><strong>Дата по:</strong> ' . $this->escape($dateTo !== '' ? $dateTo : '—') . '</div>';
        $html .= '<div><strong>Заказчик:</strong> ' . $this->escape($report['customer_name_filter'] !== '' ? $report['customer_name_filter'] : 'Все') . '</div>';
        $html .= '<div><strong>Сотрудник:</strong> ' . $this->escape($report['user_name_filter'] !== '' ? $report['user_name_filter'] : 'Все') . '</div>';
        $html .= '</div>';

        $html .= '<h2>Итоги</h2>';
        $html .= '<table><tbody>';
        $html .= '<tr><th>Записей</th><td>' . $this->escape($report['summary']['records']) . '</td></tr>';
        $html .= '<tr><th>Часов</th><td>' . $this->escape($report['summary']['hours']) . '</td></tr>';
        $html .= '<tr><th>Сумма</th><td>' . $this->escape($report['summary']['amount']) . '</td></tr>';
        $html .= '</tbody></table>';

        $html .= '<h2>По сотрудникам</h2>';
        $html .= '<table><thead><tr><th>ID</th><th>Сотрудник</th><th>Записей</th><th>Часов</th><th>Сумма</th></tr></thead><tbody>';
        if (!empty($report['employees'])) {
            foreach ($report['employees'] as $employee) {
                $html .= '<tr>';
                $html .= '<td>' . $this->escape($employee['user_id']) . '</td>';
                $html .= '<td>' . $this->escape($employee['user_name']) . '</td>';
                $html .= '<td>' . $this->escape($employee['records']) . '</td>';
                $html .= '<td>' . $this->escape($employee['hours']) . '</td>';
                $html .= '<td>' . $this->escape($employee['amount']) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="5">Нет данных</td></tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<h2>Детализация</h2>';
        $html .= '<table><thead><tr><th>ID</th><th>Дата</th><th>Время</th><th>Сотрудник</th><th>Заказчик</th><th>Место работы</th><th>Тариф</th><th>Часы</th><th>Ставка</th><th>Сумма</th></tr></thead><tbody>';
        if (!empty($report['rows'])) {
            foreach ($report['rows'] as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $this->escape($row['id']) . '</td>';
                $html .= '<td>' . $this->escape($row['work_date']) . '</td>';
                $html .= '<td>' . $this->escape($row['start_time'] . ' – ' . $row['end_time']) . '</td>';
                $html .= '<td>' . $this->escape($row['user_name']) . '</td>';
                $html .= '<td>' . $this->escape($row['customer_name']) . '</td>';
                $html .= '<td>' . $this->escape($row['workplace_name']) . '</td>';
                $html .= '<td>' . $this->escape($this->buildTariffFlagsText($row)) . '</td>';
                $html .= '<td>' . $this->escape($row['hours']) . '</td>';
                $html .= '<td>' . $this->escape($row['rate']) . '</td>';
                $html .= '<td>' . $this->escape($row['amount']) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="10">Нет данных</td></tr>';
        }
        $html .= '</tbody></table>';

        $html .= '</body></html>';

        return $html;
    }

    protected function loadDompdf()
    {
        if (class_exists('Dompdf\\Dompdf')) {
            return true;
        }

        $paths = array(
            MODX_BASE_PATH . 'vendor/autoload.php',
            MODX_CORE_PATH . 'vendor/autoload.php',
        );

        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                if (class_exists('Dompdf\\Dompdf')) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function saveDocumentRow($data)
    {
        $table = $this->getDocumentsTable();
        $sql = "INSERT INTO `{$table}`
            (`type`,`title`,`file_name`,`file_path`,`mime`,`extension`,`date_from`,`date_to`,`customer_id`,`customer_name`,`user_id`,`user_name`,`createdby`,`createdby_name`,`file_size`,`meta`,`createdon`)
            VALUES
            (:type,:title,:file_name,:file_path,:mime,:extension,:date_from,:date_to,:customer_id,:customer_name,:user_id,:user_name,:createdby,:createdby_name,:file_size,:meta,:createdon)";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return false;
        }

        return $stmt->execute($data);
    }

    public function process()
    {
        $dateFrom = trim((string)$this->getProperty('date_from'));
        $dateTo = trim((string)$this->getProperty('date_to'));
        $customerId = (int)$this->getProperty('customer_id');
        $userIdFilter = (int)$this->getProperty('user_id');

        if (!$this->ensureDocumentsTable()) {
            return $this->failure('Не удалось проверить таблицу документов');
        }

        if (!$this->loadDompdf()) {
            return $this->failure('Для генерации PDF не найден Dompdf. Установите библиотеку через Composer.');
        }

        $report = $this->collectReportData($dateFrom, $dateTo, $customerId, $userIdFilter);
        $html = $this->buildHtml($report, $dateFrom, $dateTo);

        $documentsDir = MODX_BASE_PATH . 'assets/components/crmtime/documents/';
        if (!is_dir($documentsDir) && !mkdir($documentsDir, 0775, true)) {
            return $this->failure('Не удалось создать папку для документов');
        }

        $fileName = 'report-' . date('Ymd-His') . '.pdf';
        $absolutePath = $documentsDir . $fileName;
        $relativePath = 'assets/components/crmtime/documents/' . $fileName;

        $dompdf = new \Dompdf\Dompdf(array(
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => false,
        ));
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        if (file_put_contents($absolutePath, $output) === false) {
            return $this->failure('Не удалось сохранить PDF файл');
        }

        $createdBy = $this->modx->user ? (int)$this->modx->user->get('id') : 0;
        $createdByName = '';
        if ($createdBy > 0) {
            $createdByProfile = $this->modx->getObject('modUserProfile', array('internalKey' => $createdBy));
            if ($createdByProfile && trim((string)$createdByProfile->get('fullname')) !== '') {
                $createdByName = trim((string)$createdByProfile->get('fullname'));
            } else {
                $createdByUser = $this->modx->getObject('modUser', array('id' => $createdBy));
                if ($createdByUser) {
                    $createdByName = (string)$createdByUser->get('username');
                }
            }
        }

        $titleParts = array('Отчёт crmTime');
        if ($dateFrom !== '' || $dateTo !== '') {
            $titleParts[] = ($dateFrom !== '' ? $dateFrom : '...') . ' — ' . ($dateTo !== '' ? $dateTo : '...');
        }
        $title = implode(' / ', $titleParts);

        $this->saveDocumentRow(array(
            'type' => 'report',
            'title' => $title,
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'mime' => 'application/pdf',
            'extension' => 'pdf',
            'date_from' => $dateFrom !== '' ? $dateFrom : null,
            'date_to' => $dateTo !== '' ? $dateTo : null,
            'customer_id' => $customerId,
            'customer_name' => isset($report['customer_name_filter']) ? $report['customer_name_filter'] : '',
            'user_id' => $userIdFilter,
            'user_name' => isset($report['user_name_filter']) ? $report['user_name_filter'] : '',
            'createdby' => $createdBy,
            'createdby_name' => $createdByName,
            'file_size' => filesize($absolutePath),
            'meta' => json_encode(array(
                'summary' => $report['summary'],
            )),
            'createdon' => date('Y-m-d H:i:s'),
        ));

        return $this->success('PDF сформирован и сохранён в архив документов', array(
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'file_url' => '/' . ltrim($relativePath, '/'),
        ));
    }
}

return 'CrmTimeMgrReportCreatePdfProcessor';
