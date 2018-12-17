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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    
    const REGISTRY_ITEM_KEY = 'inventorysuccess_before_order_item';
    
    /**
     * @var string
     */
    protected $process = 'place_new_order';

    /**
     * @var array
     */
    protected $orderWarehouses = array();
    
    /**
     * execute the process
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_Sales_Model_Order_Item $itemBefore
     * @return bool
     */    
    public function execute($item, $itemBefore)
    {
        if(!$this->canProcessItem($item, $itemBefore)){
            return;
        }

        $this->assignOrderItemToWarehouse($item);
        
        $this->markItemProcessed($item);
        
        return true;
    }    
    
    /**
     * Assign order item to Warehouse
     * 
     * @param Mage_Sales_Model_Order_Item $item
     */    
    public function assignOrderItemToWarehouse($item)
    {
        $this->queryProcessService->start($this->process);
        
        $this->_addWarehouseOrderItem($item); 
        $this->_decreaseAvailableQtyInOrderWarehouse($item);

        $this->queryProcessService->process($this->process);        
        
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $orderItem
     */
    protected function _addWarehouseOrderItem($item)
    {
        $warehouse = $this->getOrderWarehouse($item->getOrder());
        $warehouseOrderData = array(
            'warehouse_id' => $warehouse->getId(),
            'order_id' => $item->getOrderId(),
            'item_id' => $item->getId(),
            'product_id' => $item->getProductId(),
            'qty_ordered' => $this->_getOrderedQty($item),
            'subtotal' => $item->getRowTotal(),
            'created_at' => $item->getOrder()->getCreatedAt(),
            'updated_at' => $item->getOrder()->getUpdatedAt(),            
        );
        $this->queryProcessService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' =>  array($warehouseOrderData), 
            'table' => Mage::getResourceSingleton('inventorysuccess/warehouse_order_item')->getMainTable(),
        ), $this->process);        
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     */    
    protected function _decreaseAvailableQtyInOrderWarehouse($item)
    {
        $orderWarehouse = $this->getOrderWarehouse($item->getOrder());
        if($this->stockService->getStockId() == $this->stockService->getGlobalStockId()) {
            /* place order from global stock */
            $updateWarehouseId = $orderWarehouse->getWarehouseId();
        } else {
            /* place order from warehouse stock  */
            $updateWarehouseId = $this->stockService->getGlobalStockId();         
        }
        
        $qtyChanges = array(Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY => -$this->_getOrderedQty($item));
        $query = $this->stockRegistryService->prepareChangeProductQty($updateWarehouseId, $item->getProductId(), $qtyChanges);
        $this->queryProcessService->addQuery($query, $this->process);         
    }
    
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_Sales_Model_Order_Item $itemBefore
     * @return boolean
     */
    public function canProcessItem($item, $itemBefore)
    {
        /* check manage stock or not */
        if(!$this->isManageStock($item)) {
            return false;
        }
        /* check new item */
        if($itemBefore->getId()) {
            return false;
        }
        /* check processed item */
        if($this->isProcessedItem($item)) {
            return false;
        }
        /* check added item */
        if($this->getOrderedWarehouse($item->getId())) {
            return false;
        }
        return true;
    }    
    
    
    /**
     * Get warehouse which responds to the order
     * 
     * @param Mage_Sales_Model_Order $order
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getOrderWarehouse($order)
    {
        if(!isset($this->orderWarehouses[$order->getId()])) {
            $orderWarehouse = $this->warehouseService->getPrimaryWarehouse();
            $curStockId = $this->stockService->getStockId();
            if($curStockId != $this->stockService->getGlobalStockId()) {
                $orderWarehouse->setWarehouseId($curStockId);
                $orderWarehouse->setStockId($curStockId);
            }
            /* allow to change the order Warehouse by other extension */
            Mage::dispatchEvent('inventorysuccess_new_order_warehouse', array(
                        'order' => $order,
                        'warehouse' => $orderWarehouse
                    ));
            $this->orderWarehouses[$order->getId()] = $orderWarehouse;
        }
        return $this->orderWarehouses[$order->getId()];
    }    
}