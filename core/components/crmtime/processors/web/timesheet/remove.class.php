<?php

class CrmTimeWebTimesheetRemoveProcessor extends modProcessor
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
            SELECT t.id, t.status, t.signature_file
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

    protected function removeExistingSignatureFile($relativePath)
    {
        $relativePath = trim((string)$relativePath);
        if ($relativePath === '') {
            return;
        }

        $fullPath = MODX_BASE_PATH . ltrim($relativePath, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    protected function deleteTimesheet($id)
    {
        $table = $this->modx->getTableName('CrmTimesheet');

        $sql = "DELETE FROM {$table} WHERE id = :id";
        $stmt = $this->modx->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    protected function recalculateViolations($userId)
    {
        $assignmentIds = array();

        $ac = $this->modx->newQuery('CrmAssignment');
        $ac->where(array(
            'user_id' => (int)$userId,
        ));
        $assignments = $this->modx->getCollection('CrmAssignment', $ac);
        /** @var CrmAssignment $assignment */
        foreach ($assignments as $assignment) {
            $assignmentIds[] = (int)$assignment->get('id');
        }

        if (empty($assignmentIds)) {
            return;
        }

        $tc = $this->modx->newQuery('CrmTimesheet');
        $tc->where(array(
            'assignment_id:IN' => $assignmentIds,
        ));
        $tc->sortby('work_date', 'ASC');
        $tc->sortby('start_time', 'ASC');
        $tc->sortby('id', 'ASC');

        $timesheets = $this->modx->getCollection('CrmTimesheet', $tc);

        $timesheetIds = array();
        foreach ($timesheets as $timesheet) {
            $timesheetIds[] = (int)$timesheet->get('id');
        }

        if (!empty($timesheetIds)) {
            $vc = $this->modx->newQuery('CrmViolation');
            $vc->where(array(
                'timesheet_id:IN' => $timesheetIds,
            ));
            $violations = $this->modx->getCollection('CrmViolation', $vc);
            /** @var CrmViolation $violation */
            foreach ($violations as $violation) {
                $violation->remove();
            }
        }
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

        if ((string)$row['status'] !== 'draft') {
            return $this->failure('Nur Entwürfe können gelöscht werden.');
        }

        $vc = $this->modx->newQuery('CrmViolation');
        $vc->where(array(
            'timesheet_id' => $id,
        ));
        $violations = $this->modx->getCollection('CrmViolation', $vc);
        /** @var CrmViolation $violation */
        foreach ($violations as $violation) {
            $violation->remove();
        }

        $this->removeExistingSignatureFile(isset($row['signature_file']) ? $row['signature_file'] : '');

        if (!$this->deleteTimesheet($id)) {
            return $this->failure('Zeiteintrag konnte nicht gelöscht werden.');
        }

        $this->recalculateViolations($userId);

        return $this->success('Zeiteintrag wurde gelöscht.');
    }
}

return 'CrmTimeWebTimesheetRemoveProcessor';