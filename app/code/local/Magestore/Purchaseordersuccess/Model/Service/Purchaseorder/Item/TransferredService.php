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
class Magestore_Purchaseordersuccess_Model_Service_Purchaseorder_Item_TransferredService
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    /**
     * @var Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    protected $transferStockService;

    public function __construct()
    {
        $this->transferStockService = Magestore_Coresuccess_Model_Service::transferStockService();
    }

    /**
     * @param array $params
     * @return array
     */
    public function processTransferredData($params = array())
    {
        $result = array();
        $productIds = array_keys($params);
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $products */
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addFieldToFilter('entity_id', $productIds)
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product){
            $productId = $product->getId();
            if(in_array($productId, $productIds)){
                if(isset($params[$productId]['transfer_qty']) &&  $params[$productId]['transfer_qty'] > 0) {
                    $params[$productId]['product_id'] = $product->getId();
                    $params[$productId]['qty'] = $params[$productId]['transfer_qty'];
                    $params[$productId]['transfer_qty'] = $params[$productId]['transfer_qty'];
                    $params[$productId]['product_sku'] = $product->getSku();
                    $params[$productId]['product_name'] = $product->getName();
                    $result[$productId] = $params[$productId];
                }
            }
        }
        return $result;
    }

    /**
     * Create an empty transfer stock
     *
     * @param array $param
     * @param string $userName
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    public function createTransferStock($param = array(), $userName)
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($param['warehouse_id']);
        $purchaseOrder = $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($param['id']);
        $purchaseCode = $purchaseOrder->getPurchaseCode();
        return Mage::getModel('inventorysuccess/transferstock')
            ->setData('transferstock_code', $this->transferStockService->generateCode())
            ->setData('external_location', Mage::helper('purchaseordersuccess')->__('Purchase order #%s', $purchaseCode))
            ->setData('des_warehouse_id', $warehouse->getWarehouseId())
            ->setData('des_warehouse_code', $warehouse->getWarehouseCode())
            ->setData('reason', Mage::helper('purchaseordersuccess')->__('Transfer stock from purchase order #%s', $purchaseCode))
            ->setData('notifier_emails', '')
            ->setData('status', 'pending')
            ->setData('type', 'from_external')
            ->setData('created_by', $userName)
            ->setData('created_at', $param['transferred_at'])
            ->setId(null)
            ->save();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $transferredData
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    public function saveTransferStockData($transferStock, $transferredData = array()
    )
    {
        $data = $this->reformatPostData($transferStock, $transferredData);
        $this->transferStockService->saveTransferStockProduct($transferStock, $data);
        $this->transferStockService->updateStock($transferStock);
        $transferStock->setData('status', 'completed');
        return $transferStock->save();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transferStock
     * @param array $transferredData
     * @return array
     */
    public function reformatPostData($transferStock, $transferredData = array())
    {
        $id = $transferStock->getId();
        $newData = array();
        foreach ($transferredData as $data) {
            $item = array();
            $item['transferstock_id'] = $id;
            $item['product_id'] = $data['product_id'];
            $item['product_name'] = $data['product_name'];
            $item['product_sku'] = $data['product_sku'];
            $item['qty'] = $data['qty'];
            $newData[$data['product_id']] = $item;
        }
        return $newData;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param null $returnedQty
     * @return array
     */
    public function setQtyTransferred($purchaseItem, $transferData = array())
    {
        $qty = $purchaseItem->getQtyReceived() - $purchaseItem->getQtyTransferred() - $purchaseItem->getQtyReturned();
        if (!isset($transferData['transfer_qty']) || $transferData['transfer_qty'] > $qty){
            $transferData['transfer_qty'] = $qty;
            $transferData['qty'] = $qty;
        }
        return $transferData;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param array $transferData
     * @param array $params
     * @param null $createdBy
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder_Item_Transferred
     */
    public function prepareItemTransferred(
        $purchaseItem, $transferData = array(), $params = array(), $createdBy = null
    )
    {
        $transferData = $this->setQtyTransferred($purchaseItem, $transferData);
        return Mage::getModel('purchaseordersuccess/purchaseorder_item_transferred')
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyTransferred($transferData['transfer_qty'])
            ->setWarehouseId($params['warehouse_id'])
            ->setTransferredAt($params['transferred_at'])
            ->setCreatedBy($createdBy)
            ->setId(null);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $purchaseItem
     * @param null $transferData
     * @param array $params
     * @param null $createdBy
     * @return bool|array
     */
    public function transferItem(
        $purchaseOrder, $purchaseItem, $transferData = null, $params = array(), $createdBy = null
    )
    {
        $transferData = $this->setQtyTransferred($purchaseItem, $transferData);
        $itemTransferred = $this->prepareItemTransferred($purchaseItem, $transferData, $params, $createdBy);
        try {
            $itemTransferred->save();
            $purchaseItem->setQtyTransferred($purchaseItem->getQtyTransferred() + $itemTransferred->getQtyTransferred());
            $purchaseItem->save();
            $purchaseOrder->setTotalQtyTransferred($purchaseOrder->getTotalQtyTransferred() + $itemTransferred->getQtyTransferred());
        } catch (\Exception $e) {
            return false;
        }
        return $transferData;
    }
}