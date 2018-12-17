<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE `sales_flat_order` MODIFY `is_customer_notify` varchar(255) NULL DEFAULT '2';
	ALTER TABLE `sales_flat_quote` MODIFY `is_customer_notify` varchar(255) NULL DEFAULT '2';
");

$installer->endSetup();