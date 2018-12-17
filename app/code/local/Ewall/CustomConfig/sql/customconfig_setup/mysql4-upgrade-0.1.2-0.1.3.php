<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE sales_flat_order ADD COLUMN `is_customer_notify` varchar(255) NULL;
    ALTER TABLE sales_flat_quote ADD COLUMN `is_customer_notify` varchar(255) NULL;
");

$installer->endSetup();