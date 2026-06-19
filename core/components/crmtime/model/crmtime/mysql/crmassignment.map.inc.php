<?php

$xpdo_meta_map['CrmAssignment'] = array(
    'package' => 'crmtime',
    'version' => '1.1',
    'table' => 'crm_assignments',
    'extends' => 'xPDOObject',
    'fields' => array(
        'id' => null,
        'user_id' => 0,
        'customer_id' => 0,
        'workplace_id' => 0,
        'rate' => 0,
        'start_date' => null,
        'end_date' => null,
        'is_active' => 1,
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
        'customer_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'workplace_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'rate' => array(
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => false,
            'default' => 0.00,
        ),
        'start_date' => array(
            'dbtype' => 'date',
            'phptype' => 'date',
            'null' => true,
        ),
        'end_date' => array(
            'dbtype' => 'date',
            'phptype' => 'date',
            'null' => true,
        ),
        'is_active' => array(
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'null' => false,
            'default' => 1,
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
        'customer_id' => array(
            'alias' => 'customer_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'customer_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
        'workplace_id' => array(
            'alias' => 'workplace_id',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'workplace_id' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);