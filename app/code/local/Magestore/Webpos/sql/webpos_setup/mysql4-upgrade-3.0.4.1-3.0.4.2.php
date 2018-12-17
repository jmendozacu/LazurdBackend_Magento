<?php
$installer = $this;

$installer->startSetup();


$this->addAttribute('customer_address', 'receiver_name', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Receiver Name',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'receiver_name')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
	

$this->addAttribute('customer_address', 'is_gift', array(
    'type' => 'int',
    'input' => 'checkbox',
    'label' => 'Is Gift',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'is_gift')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();


$installer->run("
ALTER TABLE sales_flat_quote_address ADD COLUMN receiver_name varchar(255) NULL;
ALTER TABLE sales_flat_quote_address ADD COLUMN is_gift varchar(255) NULL;
ALTER TABLE sales_flat_order_address ADD COLUMN receiver_name varchar(255) NULL;
ALTER TABLE sales_flat_order_address ADD COLUMN is_gift varchar(255) NULL;
");

$installer->endSetup();
