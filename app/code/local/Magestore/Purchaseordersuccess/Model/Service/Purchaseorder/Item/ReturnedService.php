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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ReturnedService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @param array $params
     * @return array
     */
    public function processReturnedData($params = array())
    {
        $result = array();
        foreach ($params as $productId => $itemData) {
            if ($itemData['return_qty'] > 0)
                $result[$productId] = $itemData['return_qty'];
        }
        return $result;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param float|null $receivedQty
     * @return float
     */
    public function getQtyReturned($purchaseItem, $returnedQty = null)
    {
        $qty = $purchaseItem->getQtyReceived() - $purchaseItem->getQtyReturned() - $purchaseItem->getQtyTransferred();
        if(!$returnedQty || $returnedQty > $qty)
            $returnedQty = $qty;
        return $returnedQty;
    }

    /**
     * Prepare received item
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseItem
     * @param int|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Returned
     */
    public function prepareItemReturned(
        $purchaseItem, $returnedQty = null, $returnedTime = null, $createdBy = null
    )
    {
        $returnedQty = $this->getQtyReturned($purchaseItem, $returnedQty);
        return Mage::getModel('purchaseordersuccess/purchaseorder_item_returned')
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyReturned($returnedQty)
            ->setReturnedAt($returnedTime)
            ->setCreatedBy($createdBy)
            ->setId(null);
    }

    /**
     * Receive an purchase item by purchase item and qty
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param float|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return bool
     */
    public function returnItem(
        $purchaseOrder, $purchaseItem, $returnedQty = null, $returnedTime = null, $createdBy = null, $updateStock = false
    )
    {
        $returnedQty = $this->getQtyReturned($purchaseItem, $returnedQty);
        if($returnedQty == 0)
            return true;
        $itemReturned = $this->prepareItemReturned($purchaseItem, $returnedQty, $returnedTime, $createdBy);
        try{
            $itemReturned->save();
            $purchaseItem->setQtyReturned($purchaseItem->getQtyReturned()+$returnedQty);
            $purchaseItem->save();
            $purchaseOrder->setTotalQtyReturned($purchaseOrder->getTotalQtyReturned()+$returnedQty);
            if ($updateStock) {
                /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($purchaseItem->getProductId());
                if ($stockItem->getId() > 0 && $stockItem->getManageStock()) {
                    $stockItem->setQty($stockItem->getQty() - $returnedQty);
                    $stockItem->setIsInStock((int)($stockItem->getQty() > 0));
                    $stockItem->save();
                }
            }
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
}