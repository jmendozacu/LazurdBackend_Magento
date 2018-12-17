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
 * Non-warehouse Product Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Nonwarehouseproduct_Collection
    extends Mage_Catalog_Model_Resource_Product_Collection
{
    protected $MAPPING_FIELDS = array(
        'entity_id' => 'e.entity_id',
        'name' => 'catalog_product_entity_varchar.value',
        'sku' => 'e.sku',
        'price' => 'catalog_product_entity_decimal.value',
        'status' => 'catalog_product_entity_int.value',
        'qty' => 'cataloginventory_stock_item.qty'
    );

    /**
     * Initialize collection select
     * Redeclared for remove entity_type_id condition
     * in catalog_product_entity we store just products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _initSelect()
    {
        $warehouseProductIds = $this->getWarehouseProductIds();
        $productNameAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name')
            ->getId();
        $productPriceAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price')
            ->getId();
        $productStatusAttributeId = Mage::getModel('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status')
            ->getId();
        $this->getSelect()->from(
            array(self::MAIN_TABLE_ALIAS => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity'))
        )->where(self::MAIN_TABLE_ALIAS . '.type_id = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->joinLeft(
                array('catalog_product_entity_varchar' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_varchar')),
                self::MAIN_TABLE_ALIAS . ".entity_id = catalog_product_entity_varchar.entity_id && 
                catalog_product_entity_varchar.attribute_id = $productNameAttributeId",
                array('')
            )->columns(array('name' => $this->MAPPING_FIELDS['name']))
            ->joinLeft(
                array('catalog_product_entity_decimal' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_decimal')),
                self::MAIN_TABLE_ALIAS . ".entity_id = catalog_product_entity_decimal.entity_id && 
                catalog_product_entity_decimal.attribute_id = $productPriceAttributeId",
                array('')
            )->columns(array('price' => $this->MAPPING_FIELDS['price']))
            ->joinLeft(
                array('catalog_product_entity_int' => Mage::getSingleton('core/resource')
                    ->getTableName('catalog_product_entity_int')),
                self::MAIN_TABLE_ALIAS . ".entity_id = catalog_product_entity_int.entity_id && 
                catalog_product_entity_int.attribute_id = $productStatusAttributeId",
                array('')
            )->columns(array('status' => $this->MAPPING_FIELDS['status']));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->getSelect()->joinLeft(
                array('cataloginventory_stock_item' => Mage::getSingleton('core/resource')
                    ->getTableName('cataloginventory_stock_item')),
                self::MAIN_TABLE_ALIAS . ".entity_id = cataloginventory_stock_item.product_id AND stock_id = 1",
                array('qty')
            );
        }
        $this->getSelect()->group(self::MAIN_TABLE_ALIAS . '.entity_id');
        $this->getSelect()->where(
            $this->MAPPING_FIELDS['entity_id'] . ' NOT IN (?)',
            array_merge($warehouseProductIds['in_stock'], $warehouseProductIds['out_stock'])
        )->orWhere(
            $this->MAPPING_FIELDS['qty'] . ' > 0 AND ' .
            $this->MAPPING_FIELDS['entity_id'] . ' IN (?)', $warehouseProductIds['out_stock']
        );
        return $this;
    }

    /**
     * Get warehouse product in stock and out of stock ids
     *
     * @return array
     */
    public function getWarehouseProductIds()
    {
        $instockProductIds = Magestore_Coresuccess_Model_Service::warehouseStockService()
            ->getAllStocksWithProductInformation()
            ->getInStockProduct()
            ->getColumnValues('product_id');
        $outstockProductIds = Magestore_Coresuccess_Model_Service::warehouseStockService()
            ->getAllStocksWithProductInformation()
            ->getOutStockProduct()
            ->getColumnValues('product_id');
        return array('in_stock' => $instockProductIds, 'out_stock' => $outstockProductIds);
    }

    /**
     * Filter non warehouse product collection by product ids
     *
     * @param array $productIds
     * @return $this
     */
    public function addProductsToFilter($productIds = array()){
        $this->getSelect()->having($this->MAPPING_FIELDS['entity_id'].' IN (?)', $productIds);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        foreach ($this->MAPPING_FIELDS as $alias => $realField) {
            if ($alias == $field)
                $field = new Zend_Db_Expr($this->MAPPING_FIELDS[$alias]);
        }
        if (!is_array($field)) {
            $resultCondition = $this->_translateCondition($field, $condition);
        } else {
            $conditions = array();
            foreach ($field as $key => $currField) {
                $conditions[] = $this->_translateCondition(
                    $currField,
                    isset($condition[$key]) ? $condition[$key] : null
                );
            }

            $resultCondition = '(' . join(') ' . Zend_Db_Select::SQL_OR . ' (', $conditions) . ')';
        }

        $this->_select->where($resultCondition);
        return $this;
    }
    
    /**
     * @param string $columnName
     * @param array $filterValue
     * @return $this
     */
    public function addNumberToFilter($columnName, $filterValue)
    {
        if (isset($filterValue['from']))
            $this->getSelect()->having($this->MAPPING_FIELDS[$columnName] . ' >= ?', $filterValue['from']);
        if (isset($filterValue['to']))
            $this->getSelect()->having($this->MAPPING_FIELDS[$columnName] . ' <= ?', $filterValue['to']);
        return $this;
    }

    /**
     * @param string $columnName
     * @param array $filterValue
     * @return $this
     */
    public function addTextToFilter($columnName, $filterValue)
    {
        $this->getSelect()->where($this->MAPPING_FIELDS[$columnName]. ' LIKE ?', '%'.$filterValue.'%');
        return $this;
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->MAPPING_FIELDS as $alias => $realField) {
            if ($alias == $field) {
                $field = new Zend_Db_Expr($this->MAPPING_FIELDS[$alias]);
                return $this->getSelect()->order($field . ' ' . $direction);
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelect();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }
}