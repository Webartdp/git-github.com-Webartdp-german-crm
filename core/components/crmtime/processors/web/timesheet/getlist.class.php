<?php

class CrmTimeMgrTimesheetGetListProcessor extends modProcessor
{
    public function process()
    {
        $tableTimesheets = $this->modx->getTableName('CrmTimesheet');
        $tableAssignments = $this->modx->getTableName('CrmAssignment');
        $tableCustomers = $this->modx->getTableName('CrmCustomer');
        $tableWorkplaces = $this->modx->getTableName('CrmWorkplace');
        $tableUsers = $this->modx->getTableName('modUser');
        $tableProfiles = $this->modx->getTableName('modUserProfile');

        $sql = "
            SELECT
                t.id,
                t.assignment_id,
                t.work_date,
                t.start_time,
                t.end_time,
                t.status,
                t.admin_comment,
                t.signature_type,
                t.signature_file,
                t.signed_on,
                t.signed_name,
                t.is_signed,
                u.username,
                p.fullname,
                c.name AS customer_name,
                w.name AS workplace_name
            FROM {$tableTimesheets} AS t
            INNER JOIN {$tableAssignments} AS a ON a.id = t.assignment_id
            LEFT JOIN {$tableUsers} AS u ON u.id = a.user_id
            LEFT JOIN {$tableProfiles} AS p ON p.internalKey = a.user_id
            LEFT JOIN {$tableCustomers} AS c ON c.id = a.customer_id
            LEFT JOIN {$tableWorkplaces} AS w ON w.id = a.workplace_id
            ORDER BY t.work_date DESC, t.start_time DESC, t.id DESC
        ";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return $this->failure('SQL-Fehler beim Laden der Zeiteinträge.');
        }

        if (!$stmt->execute()) {
            return $this->failure('Zeiteinträge konnten nicht geladen werden.');
        }

        $rows = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hasViolation = $this->modx->getCount('CrmViolation', array(
                'timesheet_id' => (int)$row['id'],
            )) > 0 ? 1 : 0;

            $comment = trim((string)$row['admin_comment']);
            $signNote = ((int)$row['is_signed'] === 1)
                ? 'Подпись: есть / ' . trim((string)$row['signed_name']) . ' / ' . trim((string)$row['signed_on'])
                : 'Подпись: нет';

            $row['admin_comment'] = $comment !== '' ? ($comment . ' | ' . $signNote) : $signNote;
            $row['has_violation'] = $hasViolation;
            $row['signature_url'] = trim((string)$row['signature_file']) !== ''
                ? '/' . ltrim((string)$row['signature_file'], '/')
                : '';

            $rows[] = $row;
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrTimesheetGetListProcessor';