<?php

class CrmTimeMgrDocumentRemoveProcessor extends modProcessor
{
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

    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не передан ID документа');
        }

        if (!$this->ensureDocumentsTable()) {
            return $this->failure('Не удалось проверить таблицу документов');
        }

        $table = $this->getDocumentsTable();

        $sql = "SELECT * FROM `{$table}` WHERE `id` = :id LIMIT 1";
        $stmt = $this->modx->prepare($sql);

        if (!$stmt || !$stmt->execute(array(':id' => $id))) {
            return $this->failure('Не удалось найти документ');
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return $this->failure('Документ не найден');
        }

        $relativePath = trim((string)$row['file_path']);
        if ($relativePath !== '') {
            $fullPath = MODX_BASE_PATH . ltrim($relativePath, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $deleteSql = "DELETE FROM `{$table}` WHERE `id` = :id";
        $deleteStmt = $this->modx->prepare($deleteSql);

        if (!$deleteStmt || !$deleteStmt->execute(array(':id' => $id))) {
            return $this->failure('Не удалось удалить запись документа');
        }

        return $this->success('Документ удалён', array(
            'id' => $id,
        ));
    }
}

return 'CrmTimeMgrDocumentRemoveProcessor';