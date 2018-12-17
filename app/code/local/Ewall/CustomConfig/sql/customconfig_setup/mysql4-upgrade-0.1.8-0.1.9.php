<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE sales_flat_order_item ADD COLUMN `custom_options` varchar(255) NULL;
");
$installer->endSetup();