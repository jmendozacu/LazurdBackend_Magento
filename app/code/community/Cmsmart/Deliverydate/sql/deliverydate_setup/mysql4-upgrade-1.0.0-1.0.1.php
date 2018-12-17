<?php
$installer = $this;

$installer->startSetup();    

$this->_conn->dropColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_date');
$this->_conn->dropColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_comments');
$this->_conn->dropColumn($this->getTable('sales_flat_order'), 'shipping_arrival_date');
$this->_conn->dropColumn($this->getTable('sales_flat_order'), 'shipping_arrival_comments');

$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_date', 'varchar(255)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_comments', 'text');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_time_slot', 'int(11)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_delivery_date', 'datetime');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_date', 'varchar(255)');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_comments', 'text');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_time_slot', 'int(11)');
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_delivery_date', 'datetime');

$installer->endSetup(); 