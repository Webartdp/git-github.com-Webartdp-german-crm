<?php

$xpdo_meta_map['CrmWorkplace'] = array(
    'package' => 'crmtime',
    'version' => '1.1',
    'table' => 'crm_workplaces',
    'extends' => 'xPDOObject',
    'fields' => array(
        'id' => null,
        'customer_id' => 0,
        'name' => '',
        'address' => '',
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
        'customer_id' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'phptype' => 'integer',
            'null' => false,
            'default' => 0,
        ),
        'name' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ),
        'address' => array(
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
    ),
    'aggregates' => array(
        'Customer' => array(
            'class' => 'CrmCustomer',
            'local' => 'customer_id',
            'foreign' => 'id',
            'cardinality' => 'one',
            'owner' => 'foreign',
        ),
    ),
);