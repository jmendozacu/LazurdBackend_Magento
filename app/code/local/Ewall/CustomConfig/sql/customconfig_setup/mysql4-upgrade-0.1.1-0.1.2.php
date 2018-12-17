<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE guest_survey ADD COLUMN order_id int(11) unsigned NOT NULL;
");

$installer->endSetup();