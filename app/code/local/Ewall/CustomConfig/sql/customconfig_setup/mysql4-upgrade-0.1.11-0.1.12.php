<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE sales_flat_order_item ADD COLUMN `is_text_custom_option` int(11) NULL;
    ALTER TABLE sales_flat_order_item ADD COLUMN `text_custom_options_value` varchar(255) NULL;
");
$installer->endSetup();