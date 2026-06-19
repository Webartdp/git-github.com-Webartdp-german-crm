<?php

class CrmTimeWebTimesheetSubmitWithSignatureProcessor extends modProcessor
{
    protected function getCurrentUserId()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return 0;
        }

        return (int)$this->modx->user->get('id');
    }

    protected function getOwnTimesheetRow($timesheetId, $userId)
    {
        $tableTimesheets = $this->modx->getTableName('CrmTimesheet');
        $tableAssignments = $this->modx->getTableName('CrmAssignment');

        $sql = "
            SELECT t.id, t.status, t.is_signed, t.signature_file
            FROM {$tableTimesheets} AS t
            INNER JOIN {$tableAssignments} AS a ON a.id = t.assignment_id
            WHERE t.id = :id
              AND a.user_id = :user_id
            LIMIT 1
        ";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bindValue(':id', (int)$timesheetId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    protected function updateStatus($id, $status)
    {
        $table = $this->modx->getTableName('CrmTimesheet');

        $sql = "
            UPDATE {$table}
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bindValue(':status', (string)$status, PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function process()
    {
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->failure('Nicht autorisiert.', array(
                'code' => 401,
            ));
        }

        $id = (int)$this->getProperty('id');
        if ($id <= 0) {
            return $this->failure('Eintrags-ID fehlt.');
        }

        $row = $this->getOwnTimesheetRow($id, $userId);
        if (!$row) {
            return $this->failure('Zeiteintrag nicht gefunden oder kein Zugriff.');
        }

        $status = (string)$row['status'];
        if (!in_array($status, array('draft', 'rejected'), true)) {
            return $this->failure('Nur Entwürfe oder abgelehnte Einträge können eingereicht werden.');
        }

        if ((int)$row['is_signed'] !== 1 || trim((string)$row['signature_file']) === '') {
            return $this->failure('Dieser Zeiteintrag kann nicht ohne Unterschrift eingereicht werden.');
        }

        if (!$this->updateStatus($id, 'submitted')) {
            return $this->failure('Zeiteintrag konnte nicht eingereicht werden.');
        }

        return $this->success('Zeiteintrag wurde mit Unterschrift zur Freigabe eingereicht.', array(
            'id' => (int)$id,
            'status' => 'submitted',
            'is_signed' => 1,
        ));
    }
}

return 'CrmTimeWebTimesheetSubmitWithSignatureProcessor';