<?php

class CrmTimeMgrSetupInstallProcessor extends modProcessor
{
    protected function tableExists($tableName)
    {
        $cleanTableName = str_replace('`', '', $tableName);
        $sql = 'SHOW TABLES LIKE ' . $this->modx->quote($cleanTableName);
        $stmt = $this->modx->query($sql);

        return $stmt && $stmt->fetch(PDO::FETCH_NUM);
    }

    protected function columnExists($tableName, $columnName)
    {
        $cleanTableName = str_replace('`', '', $tableName);
        $sql = 'SHOW COLUMNS FROM `' . $cleanTableName . '` LIKE ' . $this->modx->quote($columnName);
        $stmt = $this->modx->query($sql);

        return $stmt && $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function addColumnIfMissing($tableName, $columnName, $definition)
    {
        if ($this->columnExists($tableName, $columnName)) {
            return 'exists';
        }

        $cleanTableName = str_replace('`', '', $tableName);
        $sql = 'ALTER TABLE `' . $cleanTableName . '` ADD COLUMN `' . $columnName . '` ' . $definition;
        $result = $this->modx->exec($sql);

        return $result !== false ? 'added' : 'error';
    }

    public function process()
    {
        $modelPath = $this->modx->getOption('core_path') . 'components/crmtime/model/';
        $this->modx->addPackage('crmtime', $modelPath);

        $manager = $this->modx->getManager();
        $classes = array(
            'CrmCustomer',
            'CrmWorkplace',
            'CrmAssignment',
            'CrmTimesheet',
            'CrmViolation',
        );

        $results = array();

        foreach ($classes as $class) {
            $tableName = $this->modx->getTableName($class);

            if ($this->tableExists($tableName)) {
                $results[] = array(
                    'class' => $class,
                    'table' => $tableName,
                    'status' => 'exists',
                );
                continue;
            }

            $created = $manager->createObjectContainer($class);

            $results[] = array(
                'class' => $class,
                'table' => $tableName,
                'status' => $created ? 'created' : 'error',
            );
        }

        $timesheetTable = $this->modx->getTableName('CrmTimesheet');

        if ($this->tableExists($timesheetTable)) {
            $results[] = array(
                'class' => 'CrmTimesheet',
                'table' => $timesheetTable,
                'column' => 'is_night',
                'status' => $this->addColumnIfMissing($timesheetTable, 'is_night', "TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_time`"),
            );

            $results[] = array(
                'class' => 'CrmTimesheet',
                'table' => $timesheetTable,
                'column' => 'is_sunday',
                'status' => $this->addColumnIfMissing($timesheetTable, 'is_sunday', "TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_night`"),
            );

            $results[] = array(
                'class' => 'CrmTimesheet',
                'table' => $timesheetTable,
                'column' => 'is_holiday',
                'status' => $this->addColumnIfMissing($timesheetTable, 'is_holiday', "TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_sunday`"),
            );
        }

        return $this->success('Проверка таблиц завершена', array(
            'results' => $results,
        ));
    }
}

return 'CrmTimeMgrSetupInstallProcessor';