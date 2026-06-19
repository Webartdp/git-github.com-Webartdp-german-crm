<?php

$xpdo_meta_map['CrmTimesheet'] = array(
    'package' => 'crmtime',
    'version' => '1.1',
    'table' => 'crm_timesheets',
    'extends' => 'xPDOObject',
    'fields' => array(
        'id' => null,
        'assignment_id' => 0,
        'work_date' => null,
        'start_time' => null,
        'end_time' => null,
        'status' => 'draft',
        'admin_comment' => '',
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
        'assignment_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'work_date' => array(
            'dbtype' => 'date',
            'phptype' => 'date',
            'null' => true,
        ),
        'start_time' => array(
            'dbtype' => 'time',
            'phptype' => 'string',
            'null' => true,
        ),
        'end_time' => array(
            'dbtype' => 'time',
            'phptype' => 'string',
            'null' => true,
        ),
        'status' => array(
            'dbtype' => 'varchar',
            'precision' => '20',
            'phptype' => 'string',
            'null' => false,
            'default' => 'draft',
        ),
        'admin_comment' => array(
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
        'assignment_id' => array(
            'alias' => 'assignment_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'assignment_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'work_date' => array(
            'alias' => 'work_date',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'work_date' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => true,
                ),
            ),
        ),
        'status' => array(
            'alias' => 'status',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'status' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);