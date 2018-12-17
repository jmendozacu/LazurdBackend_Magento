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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Api2_Product_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_GET_PRODUCT_LIST = 'get';

    /**
     *
     */
    const OPERATION_GET_PRODUCT_ALLLIST = 'list';

    const OPERATION_GET_OPTIONS = 'getoptions';

      // Islam  IR 2018
      const OPERATION_GET_WareHouse = 'getwarehouse';
      const OPERATION_Make_RequestItems = 'makeRequestItems';
    
      // Islam  IR 2018

    /**
     *
     */

    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_PRODUCT_LIST:
                $result = $this->getProductList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_PRODUCT_ALLLIST:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->getAllProductList($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_OPTIONS:
                $result = $this->getProductOptionsInformation();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
                 // Islam  IR 2018
        case self::OPERATION_GET_WareHouse:
            $result = $this->getWareHouses();
            $this->_render($result);
            $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
            break;
        case self::OPERATION_Make_RequestItems:
            $result = $this->MakeRequestItems();
            $this->_render($result);
            $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
            break;

            
            // Islam  IR 2018
        }
    }


    // Islam  IR 2018



 /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function MakeRequestItems()
    {
        try
        {
        
        $requestData = $this->getRequest()->getBodyParams();
        
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model        = Mage::getModel('inventorysuccess/transferstock');
        //
        $data = $requestData['generalData'];
        $data = json_decode($data, true);
        //
        $data['type'] = Magestore_Inventorysuccess_Model_Transferstock::TYPE_REQUEST;
        //
        //
        $transferCode = Magestore_Coresuccess_Model_Service::incrementIdService()->getNextCode(Magestore_Inventorysuccess_Model_Transferstock::TRANSFER_CODE_PREFIX);
        $data['transferstock_code'] = $transferCode;
        //
        $model->setData($data);
        if ( array_key_exists('source_warehouse_id', $data) && $data['source_warehouse_id'] ) {
            $model->setSourceWarehouseCode(
                Mage::getModel('inventorysuccess/warehouse')->load($data['source_warehouse_id'])->getWarehouseCode()
            );
        }
        if ( array_key_exists('des_warehouse_id', $data) && $data['des_warehouse_id'] ) {
            $model->setDesWarehouseCode(
                Mage::getModel('inventorysuccess/warehouse')->load($data['des_warehouse_id'])->getWarehouseCode()
            );
        }
        $model->setCreatedAt(date('Y-m-d H:i:s'));
        $model->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING);
        $uNmae = $requestData['userName'];
        $model->setCreatedBy($uNmae);
        //
        //Magestore_Coresuccess_Model_Service::transferStockService()->initTransfer($model, $data);
        $model->getResource()->save($model);

        $transfer   = $model;
        $products = $requestData['selectedProducts'];
        $products = json_decode($products, true);
       
        Mage::log($products,null,'mylogPpp.log');
        Magestore_Coresuccess_Model_Service::transferStockService()->saveTransferStockProduct($transfer, $products);
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING);
        $transfer->save();
        return "success";
    }
    catch (Exception $e) {
        return "error";
    }

    }



      /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getWareHouses()
    {
        $optionArray = array();
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection')->getTotalSkuAndQtyCollection();
        $collection->getSelect()->having('total_sku > ?', 0);
        $items = $collection->toArray(array(
                                          Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID,
                                           Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME,
                                          Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE,
                                      ));

        if ( isset($items['items']) && count($items['items']) ) {
            foreach ( $items['items'] as $item ) {
                $optionArray[$item[Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID]]
                    = Magestore_Coresuccess_Model_Service::warehouseOptionService()->_getWarehouseLabel($item);
            }
        }
        return $optionArray;
    }




// Islam  IR 2018

    /**
     * @param $params
     * @return mixed
     */
    public function getAllProductList($params)
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $isShowProductOutStock = Mage::helper('webpos')->getStoreConfig('webpos/general/show_product_outofstock');
        $itemIds = $params['itemsId'];
        $productMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product';
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('entity_id', array('in' => $itemIds));
        $store = $this->_getStore();
        $collection->setStoreId($store->getId());
        $collection->addAttributeToSelect('*')->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
            'left')
            ->getSelect()
            ->columns('entity_id AS id')
        ;

        /* allow to apply custom filters */
        Mage::dispatchEvent('webpos_catalog_product_collection_filter', array('collection' => $collection));

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }

        /* @var Varien_Data_Collection_Db $customerCollection */
        $this->_applyFilterTo($collection);
        $result['total_count'] = $collection->getSize();
        $collection->load();
        $collection->addCategoryIds();

        $products = array();
        foreach ($collection as $productModel) {
            $productModel = Mage::getModel('webpos/catalog_product')->load($productModel->getId());
            $item = $productModel->getData();
            $item['category_ids'] = $productModel->getCategoryIds();
            $item['available_qty'] = $productModel->getStockItem()->getQty();
            $item['final_price'] = $productModel->getFinalPrice();
            if($productModel->getImage() && $productModel->getImage() != 'no_selection'){
                $item['image'] = $productMedia.$item['image'];
            } else {
                $item['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).
                    'magestore/webpos/catalog/category/image.jpg';
            }

            if ($item['stock_item']['is_in_stock']) {
                $item['isShowOutStock'] = 0;
            } else {
                $item['isShowOutStock'] = 1;
            }
            $item['isBackorders'] = $item['stock_item']['backorders'];
            $item['useConfigBackorders'] = $item['stock_item']['use_config_backorders'];

            $products[] = $item;

        }
        $result['items'] = $products;


        return $result;

    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getProductList()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $productMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product';

        $collection = Mage::getModel('catalog/product')->getCollection();
        $searchAttribute = Mage::helper('webpos')->getStoreConfig('webpos/product_search/product_attribute');
        $searchAttributeArray = explode(',', $searchAttribute);
        $collection->addAttributeToSelect($searchAttributeArray);
        $collection->addAttributeToFilter('status', 1)
//            ->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()])
            ->addAttributeToFilter([
                        ['attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'],
                        ['attribute' => 'webpos_visible', 'eq' => Magestore_Webpos_Model_Source_Entity_Attribute_Source_Boolean::VALUE_YES, 'left'],
                    ],'', 'left')
           ;
        $orderField = $this->getRequest()->getOrderField();
        $orderField = 'qty_ordered';

        //Mage::log(''.$orderField, null, 'mylog3.log');

        if (null !== $orderField) {
            $collection->addAttributeToSort($orderField, $this->getRequest()->getOrderDirection());
            // $collection->addAttributeToSort($orderField , 'desc');
        }
        $session = $this->getRequest()->getParam('session');
        $storeId = Mage::getModel('webpos/user_webpossession')->getStoreIdBySession($session);
        Mage::app()->setCurrentStore($storeId);
        $collection->setStoreId($storeId);

        $collection->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=' . Mage::getModel('cataloginventory/stock')->getId(),
            'left')
            ->getSelect()
            ->columns('entity_id AS id')
            ->columns('qty_ordered AS qty_ordered')
        ;
       
       

           /*
        $collection->joinField('qty_ordered',
            'sales_bestsellers_aggregated_daily',
            'qty_ordered',
            'product_id=entity_id',
            '{{table}}.store_id=0',
            'left')
            ->getSelect();
        
        $ccc = $collection->getSelect()->__toString();
        Mage::log($ccc, null, 'mylog38.log');
*/

        /* allow to apply custom filters */
        Mage::dispatchEvent('webpos_catalog_product_collection_filter', array('collection' => $collection));

        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }


        $showOutOfStock = $this->getRequest()->getParam('show_out_stock');
        if (!$showOutOfStock) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }

        /* @var Varien_Data_Collection_Db $customerCollection */
        $this->_applyFilter($collection);
        $this->_applyFilterOr($collection);
        //$this->_applyFilterTo($collection);
        $result['total_count'] = $collection->getSize();
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        $collection->load();
        $collection->addCategoryIds();


        $products = array();
        foreach ($collection as $productModel) {
            $formatCategories = array();
            $categories = $productModel->getCategoryIds();
            foreach ($categories as $category) {
                $formatCategories[] = "'".$category."'";
            }
            $productModel = Mage::getModel('webpos/catalog_product')->load($productModel->getId());
            $stockItem = $productModel->getStockItem();
            $item = $productModel->getData();
            $item['category_ids'] = implode(' ', $formatCategories);
            $item['minimum_qty'] =  $stockItem->getMinSaleQty();
            $item['maximum_qty'] =  $stockItem->getMaxSaleQty();
            $item['qty_increment'] =  $stockItem->getQtyIncrements();
            $item['json_config'] = null;
            $item['config_options'] = null;
            $item['price_config'] = null;
            $item['custom_options'] = null;
            $item['grouped_options'] = null;
            $item['bundle_options'] = null;
            $item['id'] = $productModel->getEntityId();
            if ($this->getRequest()->getParam('status') == 'sync') {
                $item['barcode_options'] = $productModel->getBarcodeOptions();
                $item['barcode_string'] = $productModel->getBarcodeString();
                $item['search_string'] = $productModel->getSearchString();
                $item['json_config'] = $productModel->getJsonConfig();
                $item['config_options'] = $productModel->getConfigOptions();
                $item['price_config'] = $productModel->getPriceConfig();


                if($productModel->hasOptions()) {
                    foreach (Mage::getModel('catalog/product_option') ->getProductOptionCollection($productModel) as $option) {
                        if ($option->getType() === 'drop_down' || $option->getType() === 'radio'
                            || $option->getType() === 'checkbox' || $option->getType() === 'multiple') {
                            $values = Mage::getSingleton('catalog/product_option_value')->getValuesCollection($option);
                            $valueArray = array();
                            foreach ($values as $value) {
                                $valueArray[] = $value->getData();
                            }
                            $option->setData('values', $valueArray);
                        }
                        if ($option->getData('is_require')) {
                            $option->setData('is_require', true);
                        } else {
                            $option->setData('is_require', false);
                        }
                        $item['custom_options'][] = $option->getData();
                    }
                } else {
                    $item['custom_options'] = null;
                }
                if (is_array($productModel->getGroupedOptions())) {
                    $item['grouped_options'] = array_values($productModel->getGroupedOptions());
                } else {
                    $item['grouped_options'] = null;
                }
                $item['bundle_options'] = $productModel->getBundleOptions();
                if (is_array($productModel->getBundleOptions())) {
                    $item['bundle_options'] = array_values($productModel->getBundleOptions());
                } else {
                    $item['bundle_options'] = null;
                }
            }

            if ($productModel->getCustomercreditValue()) {
                $item['customercredit_value'] = $productModel->getCustomercreditValue();
            }

            if ($productModel->getStorecreditType()) {
                $item['storecredit_type'] = $productModel->getStorecreditType();
            }

            if ($productModel->getStorecreditRate()) {
                $item['storecredit_rate'] = $productModel->getStorecreditRate();
            }

            if ($productModel->getStorecreditMin()) {
                $item['storecredit_min'] = $productModel->getStorecreditMin();
            }

            if ($productModel->getStorecreditMax()) {
                $item['storecredit_max'] = $productModel->getStorecreditMax();
            }


            if ($productModel->hasOptions()) {
                $item['options'] = 1;
            } else {
                $item['options'] = 0;
            }

            $item['available_qty'] = $productModel->getStockItem()->getQty();
           // $item['final_price'] = $productModel->getFinalPrice();

            $storeId = Mage::app()->getStore()->getId();
            $discountedPrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice(
                Mage::app()->getLocale()->storeTimeStamp($storeId),
                Mage::app()->getStore($storeId)->getWebsiteId(),
                Mage::getSingleton('customer/session')->getCustomerGroupId(),
                $productModel->getId());


            if ($discountedPrice===false) { // if no rule applied for the product
                $item['final_price'] = $productModel->getFinalPrice();
            }else{
                $item['final_price'] = number_format($discountedPrice,2);
            }

            if($productModel->getImage() && $productModel->getImage() != 'no_selection'){
                $item['image'] = $productMedia.$item['image'];
            } else {
                $item['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).
                    'magestore/webpos/catalog/category/image.jpg';
            }

            if ($item['stock_item']['is_in_stock']) {
                $item['isShowOutStock'] = 0;
            } else {
                $item['isShowOutStock'] = 1;
            }
            $item['isBackorders'] = $item['stock_item']['backorders'];
            $item['useConfigBackorders'] = $item['stock_item']['use_config_backorders'];

            if (!$showOutOfStock &&  !$item['stock_item']['is_in_stock']) {
                $result['total_count'] = $result['total_count'] - 1;
            } else {
                $products[] = $item;
            }
        }
        $result['items'] = $products;
        return $result;

    }

    /**
     *
     */
    public function getProductOptionsInformation()
    {
        $productId = $this->getRequest()->getParam('id');
        $productModel = Mage::getModel('webpos/catalog_product')->load($productId);
        $item['json_config'] = $productModel->getJsonConfig();
        $item['config_options'] = $productModel->getConfigOptions();
        $item['price_config'] = $productModel->getPriceConfig();
        if($productModel->hasOptions()) {
            foreach (Mage::getModel('catalog/product_option') ->getProductOptionCollection($productModel) as $option) {
                if ($option->getType() === 'drop_down' || $option->getType() === 'radio'
                    || $option->getType() === 'checkbox' || $option->getType() === 'multiple') {
                    $values = Mage::getSingleton('catalog/product_option_value')->getValuesCollection($option);
                    $valueArray = array();
                    foreach ($values as $value) {
                        $valueArray[] = $value->getData();
                    }
                    $option->setData('values', $valueArray);
                }
                if ($option->getData('is_require')) {
                    $option->setData('is_require', true);
                } else {
                    $option->setData('is_require', false);
                }
                $item['custom_options'][] = $option->getData();
            }
        } else {
            $item['custom_options'] = null;
        }
        if (is_array($productModel->getGroupedOptions())) {
            $item['grouped_options'] = array_values($productModel->getGroupedOptions());
        } else {
            $item['grouped_options'] = null;
        }
        $item['bundle_options'] = $productModel->getBundleOptions();
        if (is_array($productModel->getBundleOptions())) {
            $item['bundle_options'] = array_values($productModel->getBundleOptions());
        } else {
            $item['bundle_options'] = null;
        }


        return $item;
    }


    /**
     * @param Varien_Data_Collection_Db $collection
     * @return $this
     */
    protected function _applyFilter(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter();


        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            $methodName = 'addAttributeToFilter';
        } elseif (method_exists($collection, 'addFieldToFilter')) {
            $methodName = 'addFieldToFilter';
        } else {
            return $this;
        }

        foreach ($filter as $filterEntry) {
            if (isset($filterEntry['in'])) {
                return $this;
            }
            $attributeCode = $filterEntry['attribute'];
            unset($filterEntry['attribute']);

            if ($attributeCode != 'category_ids') {
                try {
                    $collection->$methodName($attributeCode, $filterEntry);
                } catch(Exception $e) {
                    $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
                }
            } else {
                $categoryId = preg_replace("/[^0-9]/","",$filterEntry);
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $collection->addCategoryFilter($category)->addAttributeToSelect('*');
            }
        }

        return $this;
    }

    /**
     * get product type ids to support
     * @return array
     */
    public function getProductTypeIds()
    {
        $types = ['virtual', 'simple', 'grouped', 'bundle', 'configurable', 'customercredit'];
        return $types;
    }
    
    
}
