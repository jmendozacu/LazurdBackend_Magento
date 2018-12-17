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
class Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_ShipmentItemService
{
    
    /**
     * get warehouse id from order item id
     * 
     * @param int $itemId
     * @return int
     */
    public function getWarehouseIdByItemId($itemId)
    {
        $whOrderItem = Mage::getResourceModel('inventorysuccess/warehouse_shipment_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Shipment_Item::ORDER_ITEM_ID, $itemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whOrderItem->getWarehouseId();
    }    
    
    /**
     * get warehouse id from shipment item id
     * 
     * @param int $shipmentItemId
     * @return int
     */
    public function getWarehouseIdByShipmentItemId($shipmentItemId)
    {
        $whOrderItem = Mage::getResourceModel('inventorysuccess/warehouse_shipment_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Shipment_Item::ITEM_ID, $shipmentItemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whOrderItem->getWarehouseId();
    }        
    
    /**
     * get warehouse id from shipment id
     * 
     * @param int $shipmentId
     * @return int
     */
    public function getWarehouseIdByShipmentId($shipmentId)
    {
        $whOrderItem = Mage::getResourceModel('inventorysuccess/warehouse_shipment_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Shipment_Item::SHIPMENT_ID, $shipmentId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whOrderItem->getWarehouseId();
    }        
}