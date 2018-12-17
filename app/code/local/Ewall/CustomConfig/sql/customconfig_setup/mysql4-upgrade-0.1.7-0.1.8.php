<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    SET SQL_MODE='ALLOW_INVALID_DATES';
    ALTER TABLE `userrole_category` ADD COLUMN `pos_user` int(11) NULL;
");
$installer->endSetup();