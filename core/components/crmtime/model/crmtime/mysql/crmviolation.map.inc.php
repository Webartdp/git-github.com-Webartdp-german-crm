<?php

$xpdo_meta_map['CrmViolation'] = array(
    'package' => 'crmtime',
    'version' => '1.1',
    'table' => 'crm_violations',
    'extends' => 'xPDOObject',
    'fields' => array(
        'id' => null,
        'user_id' => 0,
        'timesheet_id' => 0,
        'related_timesheet_id' => 0,
        'direction' => '',
        'rest_hours' => 0,
        'required_hours' => 11,
        'message' => '',
        'createdon' => null,
        'updatedon' => null,
    ),
    'fieldMeta' => array(
        'id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'index' => 'pk',
            'generated' => 'native',
        ),
        'user_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'timesheet_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'related_timesheet_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'direction' => array(
            'dbtype' => 'varchar',
            'precision' => '20',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ),
        'rest_hours' => array(
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => false,
            'default' => 0.00,
        ),
        'required_hours' => array(
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => false,
            'default' => 11.00,
        ),
        'message' => array(
            'dbtype' => 'text',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ),
        'createdon' => array(
            'dbtype' => 'datetime',
            'phptype' => 'datetime',
            'null' => true,
        ),
        'updatedon' => array(
            'dbtype' => 'datetime',
            'phptype' => 'datetime',
            'null' => true,
        ),
    ),
    'indexes' => array(
        'PRIMARY' => array(
            'alias' => 'PRIMARY',
            'primary' => true,
            'unique' => true,
            'type' => 'BTREE',
            'columns' => array(
                'id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'user_id' => array(
            'alias' => 'user_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'user_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'timesheet_id' => array(
            'alias' => 'timesheet_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'timesheet_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'related_timesheet_id' => array(
            'alias' => 'related_timesheet_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'related_timesheet_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'direction' => array(
            'alias' => 'direction',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'direction' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);