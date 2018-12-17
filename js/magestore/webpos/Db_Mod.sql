ALTER TABLE `sales_flat_order_item` ADD `discount_cause` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'discount_cause'  ;
ALTER TABLE `sales_flat_order_item` ADD `discount_by` INT(10) NULL DEFAULT NULL COMMENT 'discount_by' ك

ALTER TABLE `sales_flat_quote_item` ADD `discount_cause` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'discount_cause' ;
ALTER TABLE `sales_flat_quote_item` ADD `discount_by` INT(10) NULL DEFAULT NULL COMMENT 'discount_by' 

ALTER TABLE `webpos_user` ADD `head_user_id` INT(10) NULL DEFAULT NULL COMMENT 'head_user_id' ;
