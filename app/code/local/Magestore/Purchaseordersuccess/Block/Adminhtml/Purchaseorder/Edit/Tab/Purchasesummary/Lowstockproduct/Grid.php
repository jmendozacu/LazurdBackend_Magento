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
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Item as PurchaseorderItem;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Lowstockproduct_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Abstractmodalgrid
{
    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'purchase_order_low_stock_product_list';

    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_low_stock_products';

    /**
     * @var string
     */
    protected $modalName = 'lowstockproduct';

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $rate = $this->purchaseOrder->getCurrencyRate();
        /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection $collection */
        $collection = Mage::getResourceModel('suppliersuccess/supplier_product_collection');
        $collection->getSelect()->columns(array(
            'cost' => "ROUND(main_table.cost * {$rate}, 2)",
            'qty_orderred' => "(0)",
        ));
        $supplierId = $this->getRequest()->getParam('supplier_id', null);
        $notificationId = $this->getRequest()->getParam('notification_id', null);
        $collection->addFieldToFilter('supplier_id', $supplierId);
        if ($supplierId) {
            $productIds = $this->purchaseOrder->getItems()->getColumnValues('product_id');
            if (!empty($productIds))
                $collection->addFieldToFilter('main_table.product_id', array('nin' => $productIds));
        }
        if ($notificationId) {
            $collection->getSelect()->joinInner(
                array(
                    'lowstock_product' => $collection->getResource()
                        ->getReadConnection()
                        ->getTableName('os_lowstock_notification_product')
                ),
                'main_table.product_id = lowstock_product.product_id 
                    AND lowstock_product.notification_id = ' . $notificationId,
                array('current_qty')
            );
        } else {
            $collection->addFieldToFilter('product_id', 0);
        }
        return $collection;
    }

    /**
     * Modify modal grid columns
     *
     * @return $this
     */
    protected function modifyColumn(){
        $this->addColumnAfter("current_qty",
            array(
                "header" => $this->__("Current Qty"),
                "index" => "current_qty",
            ),
            'product_name'
        );
        return $this;
    }
}