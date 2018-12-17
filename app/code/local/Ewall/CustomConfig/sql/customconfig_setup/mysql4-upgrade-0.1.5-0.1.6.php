<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE `sales_flat_order` ADD COLUMN `is_delay_notify` int(11) unsigned NOT NULL DEFAULT '0';
");

$installer->endSetup();