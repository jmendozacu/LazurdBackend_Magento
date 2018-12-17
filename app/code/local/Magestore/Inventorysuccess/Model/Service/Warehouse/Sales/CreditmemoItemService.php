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
class Magestore_Inventorysuccess_Model_Service_Warehouse_Sales_CreditmemoItemService
{
    /**
     * get warehouse id from order item id
     * 
     * @param int $itemId
     * @return int
     */
    public function getWarehouseIdByItemId($itemId)
    {
        $whItem = Mage::getResourceModel('inventorysuccess/warehouse_creditmemo_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Creditmemo_Item::ORDER_ITEM_ID, $itemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whItem->getWarehouseId();
    }      
    
    /**
     * get warehouse id from creditmemo item id
     * 
     * @param int $creditmemoItemId
     * @return int
     */
    public function getWarehouseIdByCreditmemoItemId($creditmemoItemId)
    {
        $whItem = Mage::getResourceModel('inventorysuccess/warehouse_creditmemo_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Creditmemo_Item::ITEM_ID, $creditmemoItemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();
        return $whItem->getWarehouseId();
    }     
    
    /**
     * 
     * @param int $creditmemoItemId
     * @return Magestore_Inventorysuccess_Model_Warehouse_Creditmemo_Item
     */
    public function getWarehouseRefundItem($creditmemoItemId)
    {
        return Mage::getResourceModel('inventorysuccess/warehouse_creditmemo_item_collection')
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse_Creditmemo_Item::ITEM_ID, $creditmemoItemId)
                        ->setPageSize(1)->setCurPage(1)
                        ->getFirstItem();        
    }
}