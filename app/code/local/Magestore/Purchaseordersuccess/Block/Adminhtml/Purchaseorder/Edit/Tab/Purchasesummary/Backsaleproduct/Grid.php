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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Backsaleproduct_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Abstractmodalgrid
{
    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'purchase_order_back_sale_product_list';
    
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_back_sale_products';

    /**
     * @var string
     */
    protected $modalName = 'backsaleproduct';

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $rate = $this->purchaseOrder->getCurrencyRate();
        /** @var Magestore_Purchaseordersuccess_Model_Mysql4_Catalog_Product_Collection $collection */
        $collection = Mage::getResourceModel('purchaseordersuccess/catalog_product_collection');
        $readAdapter = $collection->getResource()
            ->getReadConnection();
        $supplierId = $this->getRequest()->getParam('supplier_id', null);
        if (!$supplierId) {
            $collection->addFieldToFilter('entity_id', 0);
        } else {
            $collection->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'inner'
            )->getSelect()->where('at_qty.qty < 0');
            $conditions = 'e.entity_id = supplier_product.product_id';
            if ($supplierId)
                $conditions .= ' AND supplier_product.supplier_id = ' . $supplierId;
            $collection->getSelect()->joinInner(
                array('supplier_product' => $readAdapter->getTableName('os_supplier_product')),
                $conditions,
                array('product_id', 'product_sku', 'product_supplier_sku', 'product_name')
            )->columns(array(
                'cost' => "ROUND(supplier_product.cost * {$rate}, 2)",
                'qty_orderred' => "(0)",
            ));
            $productIds = $this->purchaseOrder->getItems()->getColumnValues(PurchaseorderItem::PRODUCT_ID);
            if (!empty($productIds))
                $collection->getSelect()
                    ->where("e.entity_id NOT IN ('" . implode("','", $productIds) . "')");
        }
        return $collection;
    }

    /**
     * Modify modal grid columns
     * 
     * @return $this
     */
    protected function modifyColumn(){
        $this->addColumnAfter("qty",
            array(
                "header" => $this->__("Product Qty"),
                "index" => "qty",
                "type"  => "number"
            ),
            'product_name'
        );
        return $this;
    }
}