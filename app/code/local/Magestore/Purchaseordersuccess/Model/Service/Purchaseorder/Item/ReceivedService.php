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
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_ReceivedService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @param array $params
     * @return array
     */
    public function processReceivedData($params = array())
    {
        $result = array();
        foreach ($params as $productId => $itemData) {
            if ($itemData['receive_qty'] > 0)
                $result[$productId] = $itemData['receive_qty'];
        }
        return $result;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param float|null $receivedQty
     * @return float
     */
    public function getQtyReceived($purchaseItem, $receivedQty = null)
    {
        $qty = $purchaseItem->getQtyOrderred() - $purchaseItem->getQtyReceived();
        if (!$receivedQty || $receivedQty > $qty)
            $receivedQty = $qty;
        return $receivedQty;
    }

    /**
     * Prepare received item
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseItem
     * @param int|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Received
     */
    public function prepareItemReceived(
        $purchaseItem, $receivedQty = null, $receivedTime = null, $createdBy = null
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        return Mage::getModel('purchaseordersuccess/purchaseorder_item_received')
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyReceived($receivedQty)
            ->setReceivedAt($receivedTime)
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
    public function receiveItem(
        $purchaseOrder, $purchaseItem, $receivedQty = null, $receivedTime = null, $createdBy = null, $updateStock = false
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        if ($receivedQty == 0)
            return true;
        $itemReceived = $this->prepareItemReceived($purchaseItem, $receivedQty, $receivedTime, $createdBy);
        try {
            $itemReceived->save();
            $purchaseItem->setQtyReceived($purchaseItem->getQtyReceived() + $receivedQty);
            $purchaseItem->save();
            $purchaseOrder->setTotalQtyReceived($purchaseOrder->getTotalQtyReceived() + $receivedQty);
            if ($updateStock) {
                /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($purchaseItem->getProductId());
                if ($stockItem->getId() > 0 && $stockItem->getManageStock()) {
                    $stockItem->setQty($stockItem->getQty() + $receivedQty);
                    $stockItem->setIsInStock((int)($stockItem->getQty() > 0));
                    $stockItem->save();
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}