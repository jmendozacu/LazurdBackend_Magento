<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE userrole_category ADD COLUMN allow_store_id text(63) NULL;
");
$installer->endSetup();