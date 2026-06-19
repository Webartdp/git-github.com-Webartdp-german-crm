<?php

$xpdo_meta_map['CrmCustomer'] = array(
    'package' => 'crmtime',
    'version' => '1.1',
    'table' => 'crm_customers',
    'extends' => 'xPDOObject',
    'fields' => array(
        'id' => null,
        'name' => '',
        'code' => '',
        'description' => '',
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
        'name' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ),
        'code' => array(
            'dbtype' => 'varchar',
            'precision' => '100',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ),
        'description' => array(
            'dbtype' => 'text',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
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
        'name' => array(
            'alias' => 'name',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'name' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);