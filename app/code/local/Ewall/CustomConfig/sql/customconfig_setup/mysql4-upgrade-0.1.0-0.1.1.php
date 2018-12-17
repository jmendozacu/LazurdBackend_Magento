<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('guest_survey')};
    CREATE TABLE {$this->getTable('guest_survey')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `guest_name` varchar(255) NULL default '',
      `guest_email` varchar(255) NULL default '',
      `survey_option` varchar(255) NULL default '2',
      `survey_message` varchar(255) NULL default '',
      PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ALTER TABLE `guest_survey` ADD `is_mail_sent`  int(11) unsigned NULL DEFAULT '0' AFTER `survey_message`;
");

$installer->endSetup();