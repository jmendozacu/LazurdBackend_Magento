<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('userrole_status')};
CREATE TABLE {$this->getTable('userrole_status')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `role_id` int(11) unsigned NOT NULL,
  `viewd_status` varchar(255) NULL default '',
  `allowed_status` varchar(255) NULL default '',
  `rolename` varchar(255) NULL default '',  
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('userrole_category')};
CREATE TABLE {$this->getTable('userrole_category')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `user_roles` int(11) unsigned NOT NULL,
  `cat_ids` varchar(255) NULL default '',
  
  `rolename` varchar(255) NULL default '',
  
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();