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
class Magestore_Purchaseordersuccess_Model_Service_Barcode_ScanService
{
    /**
     * @param int $supplierId
     * @param string $barcode
     * @return Magestore_Barcodesuccess_Model_Mysql4_Barcode_Collection
     */
    public function searchBarcode($supplierId, $barcode){
        /** @var Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection $collection */
        $collection = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
            ->addFieldToFilter('main_table.supplier_id', $supplierId)
            ->addFieldToSelect(array('product_name', 'product_supplier_sku', 'cost'));
        $collection->getSelect()->joinInner(
            array('barcode' => $collection->getResource()->getReadConnection()->getTableName('os_barcode')),
            'main_table.product_id = barcode.product_id',
            '*'
        );
        $collection->addFieldToFilter('barcode.' . Magestore_Barcodesuccess_Model_Barcode::BARCODE, $barcode);
        return $collection;
    }
    
    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param array $barcodes
     * @return array
     */
    public function prepareSelectedItem($purchaseOrder, $barcodes = array(), $qtyVariable = 'qty_orderred', $isPriceList = false)
    {
        $selectedItems = $productSku = array();
        foreach ($barcodes as $barcode) {
            if (in_array($barcode['product_id'], array_keys($selectedItems))) {
                $selectedItems[$barcode['product_id']][$qtyVariable] += $barcode['qty'];
            } else {
                $selectedItems[$barcode['product_id']] = array(
                    $qtyVariable => $barcode['qty'],
                    'cost' => $barcode['cost']
                );
                $productSku[] = $barcode['product_sku'];
            }
        }
        if ($isPriceList) {
            $time = $purchaseOrder->getPurchasedAt();
            $priceListJson = Mage::getModel('purchaseordersuccess/supplier')
                ->getPriceListJson($purchaseOrder->getSupplierId(), $productSku, $time, $purchaseOrder);
            if (!empty($priceListJson)) {
                foreach ($selectedItems as $productId => $data) {
                    $minCost = 0;
                    foreach ($priceListJson as $price) {
                        if ($price['product_id'] == $productId
                            && floatval($price['minimal_qty']) <= floatval($data[$qtyVariable])
                            && floatval($price['cost'] > 0)
                        ) {
                            if ($minCost == 0) {
                                $minCost = $price['cost'];
                            } else if (floatval($price['cost']) < floatval($minCost)) {
                                $minCost = $productSku['cost'];
                            }
                        }
                    }

                    if (floatval($minCost) > 0) {
                        $data['cost'] = $minCost;
                        $selectedItems[$productId] = $data;
                    }
                }
            }
        }
        return $selectedItems;
    }
}