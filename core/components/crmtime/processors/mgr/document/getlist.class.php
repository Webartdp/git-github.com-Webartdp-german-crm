<?php

class CrmTimeMgrDocumentGetListProcessor extends modProcessor
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
        if (!$this->ensureDocumentsTable()) {
            return $this->failure('Не удалось проверить таблицу документов');
        }

        $table = $this->getDocumentsTable();
        $sql = "SELECT * FROM `{$table}` ORDER BY `id` DESC";
        $stmt = $this->modx->prepare($sql);

        if (!$stmt || !$stmt->execute()) {
            return $this->failure('Не удалось загрузить документы');
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!is_array($rows)) {
            $rows = array();
        }

        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['customer_id'] = (int)$row['customer_id'];
            $row['user_id'] = (int)$row['user_id'];
            $row['createdby'] = (int)$row['createdby'];
            $row['file_size'] = (float)$row['file_size'];
        }
        unset($row);

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrDocumentGetListProcessor';