<?php
$installer = $this;

$installer->startSetup();

$installer->run("

 DROP TABLE IF EXISTS {$this->getTable('deliverydate/deliverydate')};
    CREATE TABLE {$this->getTable('deliverydate/deliverydate')}(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `fromtime` time NOT NULL,
    `totime` time NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
 DROP TABLE IF EXISTS {$this->getTable('deliverydate/holiday')};
    CREATE TABLE {$this->getTable('deliverydate/holiday')}(
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
    
    ");
    
// $this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_date', 'varchar(255)');
// $this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_comments', 'text');
// $this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_date', 'varchar(255)');
// $this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_comments', 'text');

$installer->endSetup(); 