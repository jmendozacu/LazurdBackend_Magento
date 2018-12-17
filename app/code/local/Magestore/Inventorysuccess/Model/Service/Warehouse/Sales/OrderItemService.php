<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_OrderItemService
{
    
    /**
     * get warehouse id from order item id
     * 
     * @param int $itemId
     * @return int
     */
    public function getWarehouseIdByItemId($itemId)
    {
        $whOrderItem = Mage::getResourceModel('inventorysuccess/warehouse_order_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Order_Item::ITEM_ID, $itemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whOrderItem->getWarehouseId();
    }
    
    /**
     * Get WarehouseIds by order item Ids
     * 
     * @param array $itemIds
     * @return array
     */    
    public function getWarehousesByItemIds($itemIds)
    {
        $warehouseIds = array();
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_order_item_collection')
                    ->addFieldToSelect(array(
                        Magestore_Inventorysuccess_Model_Warehouse_Order_Item::ITEM_ID,
                        Magestore_Inventorysuccess_Model_Warehouse_Order_Item::WAREHOUSE_ID
                    ))
                    ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Order_Item::ITEM_ID, array('in' => $itemIds));
        if($collection->getSize()) {
            foreach($collection as $item) {
                $warehouseIds[$item->getItemId()] = $item->getWarehouseId();
            }
        }
        return $warehouseIds;
    }
    
    /**
     * Check existed orderItem in Warehouse
     * 
     * @param int $itemId
     * @return bool
     */    
    public function isExisted($itemId)
    {   
        if($this->getWarehouseIdByItemId()) {
            return true;
        }
        return false;
    }

    /**
     * Check item has been canceled or not from warehouse
     * 
     * @param int $itemId
     * @return bool
     */
    public function isCanceledItem($itemId) 
    {
        $whOrderItem = Mage::getResourceModel('inventorysuccess/warehouse_order_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Order_Item::ITEM_ID, $itemId)
                        ->addFieldToFilter('qty_canceled', array('gt' => 0))
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();  
        if($whOrderItem->getId()) {
            return true;
        }
        return false;
    }
    
    
    /**
     * Prepare query to change total_qty, qty_to_ship of orderItem in warehouse
     * Do not commit query
     * 
     * @param int $warehouseId
     * @param int $itemId
     * @param array $changeQtys
     * @return array
     */
    public function prepareChangeItemQty($warehouseId, $itemId, $changeQtys)
    {
        if(!count($changeQtys)) {
            return array();
        }
        $values = array();
        foreach($changeQtys as $field => $qtyChange) {
            $operation = $qtyChange > 0 ? '+' : '-';
            $values[$field] = new Zend_Db_Expr($field.$operation.abs($qtyChange));
        }
        $where = array('item_id=?' => $itemId, 'warehouse_id=?' => $warehouseId);

        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $values,
            'condition' => $where,
            'table' => Mage::getResourceModel('inventorysuccess/warehouse_order_item')->getMainTable()         
        );
        return $query;
    }  
}