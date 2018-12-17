<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE sales_flat_order_status_history ADD COLUMN custom_order_status varchar(255) NULL;
");

$installer->endSetup();
