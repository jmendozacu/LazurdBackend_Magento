<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('po_pdf/template')} (
    `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `title` VARCHAR( 255 ) NOT NULL ,
    `content` TEXT NOT NULL ,
    PRIMARY KEY ( `id` )
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
");
$installer->endSetup();