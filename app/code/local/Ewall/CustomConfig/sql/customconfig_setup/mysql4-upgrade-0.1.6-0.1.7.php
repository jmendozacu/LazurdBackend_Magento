<?php 
$installer = $this;
$installer->startSetup(); 
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_grid'),
        'webpos_staff_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 11,
            'unsigned'  => true,
            'nullable' => false,
            'comment' => 'Webpos Staff Id'
        )
    ); 
$installer->endSetup();