<?php
$installer = $this;

$installer->startSetup();


$this->addAttribute('customer_address', 'customer_lat', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Current Latitude',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'customer_lat')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
	

$this->addAttribute('customer_address', 'customer_lon', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Current Longitude',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'customer_lon')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();


$installer->run("
ALTER TABLE sales_flat_quote_address ADD COLUMN customer_lat varchar(255) NULL;
ALTER TABLE sales_flat_quote_address ADD COLUMN customer_lon varchar(255) NULL;
ALTER TABLE sales_flat_order_address ADD COLUMN customer_lat varchar(255) NULL;
ALTER TABLE sales_flat_order_address ADD COLUMN customer_lon varchar(255) NULL;
");

$installer->endSetup();
