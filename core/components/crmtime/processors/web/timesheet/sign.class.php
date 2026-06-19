<?php

class CrmTimeWebTimesheetSignProcessor extends modProcessor
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
            SELECT t.id, t.assignment_id, t.status, t.signature_file, t.is_signed
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

    protected function getSignatureState($timesheetId)
    {
        $tableTimesheets = $this->modx->getTableName('CrmTimesheet');

        $sql = "
            SELECT id, signature_type, signature_file, signed_on, signed_ip, signed_name, is_signed
            FROM {$tableTimesheets}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bindValue(':id', (int)$timesheetId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    protected function getSignedName($userId)
    {
        $user = $this->modx->getObject('modUser', array(
            'id' => (int)$userId,
        ));

        if (!$user) {
            return '';
        }

        $profile = $user->getOne('Profile');
        if ($profile) {
            $fullname = trim((string)$profile->get('fullname'));
            if ($fullname !== '') {
                return $fullname;
            }
        }

        return (string)$user->get('username');
    }

    protected function getClientIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return trim($_SERVER['REMOTE_ADDR']);
        }

        return '';
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

    protected function ensureSignatureDirectory()
    {
        $dir = MODX_BASE_PATH . 'assets/components/crmtime/signatures/';
        if (is_dir($dir)) {
            return $dir;
        }

        if (@mkdir($dir, 0755, true)) {
            return $dir;
        }

        return '';
    }

    protected function saveDataUrlSignature($dataUrl, $timesheetId, $userId)
    {
        $dataUrl = trim((string)$dataUrl);
        if ($dataUrl === '') {
            return array(false, '', 'Signaturdaten fehlen.');
        }

        if (!preg_match('#^data:image/(png|jpeg|jpg);base64,#i', $dataUrl, $match)) {
            return array(false, '', 'Ungültiges Signaturformat.');
        }

        $ext = strtolower($match[1]);
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $base64 = preg_replace('#^data:image/(png|jpeg|jpg);base64,#i', '', $dataUrl);
        $binary = base64_decode($base64, true);

        if ($binary === false || $binary === '') {
            return array(false, '', 'Signatur konnte nicht decodiert werden.');
        }

        $dir = $this->ensureSignatureDirectory();
        if ($dir === '') {
            return array(false, '', 'Signaturordner konnte nicht erstellt werden.');
        }

        $fileName = 'timesheet-' . (int)$timesheetId . '-user-' . (int)$userId . '-' . date('Ymd-His') . '.' . $ext;
        $fullPath = $dir . $fileName;

        if (file_put_contents($fullPath, $binary) === false) {
            return array(false, '', 'Signaturdatei konnte nicht gespeichert werden.');
        }

        return array(true, 'assets/components/crmtime/signatures/' . $fileName, '');
    }

    protected function saveUploadedSignature($file, $timesheetId, $userId)
    {
        if (empty($file) || !is_array($file) || empty($file['tmp_name'])) {
            return array(false, '', 'Signaturdatei fehlt.');
        }

        if (!empty($file['error'])) {
            return array(false, '', 'Fehler beim Hochladen der Signaturdatei.');
        }

        $name = isset($file['name']) ? (string)$file['name'] : '';
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($ext, array('png', 'jpg', 'jpeg'), true)) {
            return array(false, '', 'Nur PNG und JPG sind erlaubt.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return array(false, '', 'Die hochgeladene Datei ist kein gültiges Bild.');
        }

        if (!in_array($imageInfo['mime'], array('image/png', 'image/jpeg'), true)) {
            return array(false, '', 'Nur PNG und JPG sind erlaubt.');
        }

        $dir = $this->ensureSignatureDirectory();
        if ($dir === '') {
            return array(false, '', 'Signaturordner konnte nicht erstellt werden.');
        }

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $fileName = 'timesheet-' . (int)$timesheetId . '-user-' . (int)$userId . '-' . date('Ymd-His') . '.' . $ext;
        $fullPath = $dir . $fileName;

        if (!@move_uploaded_file($file['tmp_name'], $fullPath)) {
            if (!@copy($file['tmp_name'], $fullPath)) {
                return array(false, '', 'Signaturdatei konnte nicht gespeichert werden.');
            }
        }

        return array(true, 'assets/components/crmtime/signatures/' . $fileName, '');
    }

    protected function updateSignatureFields($id, $signatureType, $filePath, $signedOn, $signedIp, $signedName)
    {
        $table = $this->modx->getTableName('CrmTimesheet');

        $sql = "
            UPDATE {$table}
            SET signature_type = :signature_type,
                signature_file = :signature_file,
                signed_on = :signed_on,
                signed_ip = :signed_ip,
                signed_name = :signed_name,
                is_signed = 1
            WHERE id = :id
        ";

        $stmt = $this->modx->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bindValue(':signature_type', (string)$signatureType, PDO::PARAM_STR);
        $stmt->bindValue(':signature_file', (string)$filePath, PDO::PARAM_STR);
        $stmt->bindValue(':signed_on', (string)$signedOn, PDO::PARAM_STR);
        $stmt->bindValue(':signed_ip', (string)$signedIp, PDO::PARAM_STR);
        $stmt->bindValue(':signed_name', (string)$signedName, PDO::PARAM_STR);
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
            return $this->failure('Nur Entwürfe oder abgelehnte Einträge können unterschrieben werden.');
        }

        $signatureType = trim((string)$this->getProperty('signature_type'));
        $signatureData = $this->getProperty('signature_data');

        $ok = false;
        $filePath = '';
        $message = '';

        if ($signatureType === 'draw') {
            list($ok, $filePath, $message) = $this->saveDataUrlSignature($signatureData, $id, $userId);
        } elseif ($signatureType === 'upload') {
            $uploadFile = isset($_FILES['signature_upload']) ? $_FILES['signature_upload'] : null;
            list($ok, $filePath, $message) = $this->saveUploadedSignature($uploadFile, $id, $userId);
        } else {
            return $this->failure('Unbekannter Signaturtyp.');
        }

        if (!$ok) {
            return $this->failure($message !== '' ? $message : 'Signatur konnte nicht gespeichert werden.');
        }

        $this->removeExistingSignatureFile(isset($row['signature_file']) ? $row['signature_file'] : '');

        $signedOn = date('Y-m-d H:i:s');
        $signedIp = $this->getClientIp();
        $signedName = $this->getSignedName($userId);

        if (!$this->updateSignatureFields($id, $signatureType, $filePath, $signedOn, $signedIp, $signedName)) {
            return $this->failure('Signaturdaten konnten nicht gespeichert werden.');
        }

        $state = $this->getSignatureState($id);
        if (!$state || (int)$state['is_signed'] !== 1 || trim((string)$state['signature_file']) === '') {
            return $this->failure('Подпись не записалась в БД после сохранения файла.', array(
                'db_state' => $state,
            ));
        }

        return $this->success('Signatur wurde gespeichert.', array(
            'id' => (int)$id,
            'is_signed' => 1,
            'signature_type' => (string)$state['signature_type'],
            'signature_file' => (string)$state['signature_file'],
            'signature_url' => '/' . ltrim((string)$state['signature_file'], '/'),
            'signed_on' => (string)$state['signed_on'],
            'signed_name' => (string)$state['signed_name'],
            'db_state' => $state,
        ));
    }
}

return 'CrmTimeWebTimesheetSignProcessor';