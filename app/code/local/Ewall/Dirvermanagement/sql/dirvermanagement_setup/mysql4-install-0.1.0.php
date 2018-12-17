<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table driver_management(id int not null auto_increment, userid int(11) unsigned NULL,mobile varchar(100), unique_id varchar(100),longitude varchar(100),latitude varchar(100) , updated_at datetime NULL ,primary key(id));
SQLTEXT;

$installer->run($sql);
//demo
//Mage::getModel('core/url_rewrite')->setId(null);
//demo
$installer->endSetup();
