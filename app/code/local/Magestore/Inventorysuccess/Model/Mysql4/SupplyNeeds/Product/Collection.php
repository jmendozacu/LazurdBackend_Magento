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
 * Adjuststock Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_SupplyNeeds_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     *
     * @var bool
     */
    protected $_isGroupSql = false;

    /*
     * @var bool
     */
    protected $_resetHaving = false;

//    public function _construct()
//    {
//        parent::_construct();
//        $this->_init('inventorysuccess/supplyNeeds_product');
//    }

    /**
     * @param $value
     * @return $this
     */
    public function setIsGroupCountSql($value) {
        $this->_isGroupSql = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setResetHaving($value) {
        $this->_resetHaving = $value;
        return $this;
    }
    
    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        if ($this->_isGroupSql) {
            $this->_renderFilters();
            $countSelect = clone $this->getSelect();
            $countSelect->reset(\Zend_Db_Select::ORDER);
            $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
            $countSelect->reset(\Zend_Db_Select::COLUMNS);
            if (count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0) {
                //$countSelect->reset(Zend_Db_Select::GROUP);
                if ($this->_resetHaving) {
                    $countSelect->reset(\Zend_Db_Select::HAVING);
                }
                //$countSelect->distinct(true);
                $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
                $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
            } else {
                $countSelect->columns('COUNT(*)');
            }
            return $countSelect;
        }
        return parent::getSelectCountSql();
    }
}