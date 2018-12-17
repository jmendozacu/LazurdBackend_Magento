<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE userrole_category ADD COLUMN allow_store_switcher int(11) NOT NULL;
    ALTER TABLE userrole_category ADD COLUMN allow_order_count int(11) NOT NULL;
    ALTER TABLE userrole_category ADD COLUMN allow_order_total int(11) NOT NULL;
");
$installer->endSetup();