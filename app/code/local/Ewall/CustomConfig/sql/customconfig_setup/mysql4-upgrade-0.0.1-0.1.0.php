<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE sales_flat_order_item ADD COLUMN category_id varchar(255) NULL;
    ALTER TABLE sales_flat_order_item ADD COLUMN item_status varchar(255) NULL;

    ALTER TABLE sales_flat_quote_item ADD COLUMN category_id varchar(255) NULL;
    ALTER TABLE sales_flat_quote_item ADD COLUMN item_status varchar(255) NULL;

    ALTER TABLE sales_flat_order ADD COLUMN kitchen_user_ids varchar(255) NULL;
    ALTER TABLE sales_flat_order ADD COLUMN order_status varchar(255) NULL;
    ALTER TABLE sales_flat_order ADD COLUMN driver_id varchar(255) NULL;

    ALTER TABLE sales_flat_quote ADD COLUMN kitchen_user_ids varchar(255) NULL;
    ALTER TABLE sales_flat_quote ADD COLUMN order_status varchar(255) NULL;

    ALTER TABLE sales_flat_order_grid ADD COLUMN driver_id varchar(255) NULL;
    ALTER TABLE sales_flat_order_grid ADD COLUMN order_status varchar(255) NULL;
    ALTER TABLE sales_flat_order_grid ADD COLUMN kitchen_user_ids varchar(255) NULL;
");

$installer->endSetup();