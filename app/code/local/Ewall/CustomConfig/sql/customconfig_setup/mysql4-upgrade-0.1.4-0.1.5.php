<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE `sales_flat_order` ADD COLUMN `is_survey` int(11) unsigned NOT NULL DEFAULT '1';
    ALTER TABLE `sales_flat_order_grid` ADD COLUMN `is_survey` int(11) unsigned NOT NULL DEFAULT '1';
");

$installer->endSetup();