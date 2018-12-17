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
 * Warehouse Producr Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Warehouse_ProductBak extends Mage_Core_Model_Abstract
{

    CONST WAREHOUSE_PRODUCT_ID = "warehouse_product_id";

    CONST WAREHOUSE_ID = "warehouse_id";

    const PRODUCT_ID = "product_id";

    const TOTAL_QTY = "total_qty";

    CONST QTY_TO_SHIP = "qty_to_ship";

    CONST SHELF_LOCATION = "shelf_location";

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/warehouse_product');
    }
    
    /**
     * @return int|null
     */
    public function getWarehouseProductId()
    {
        return $this->getData(self::WAREHOUSE_PRODUCT_ID);
    }

    /**
     * @return int|null
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);      
    }

    /**
     * @return int
     */
    public function getTotalQty()
    {
        return $this->getData(self::TOTAL_QTY);
    }

    /**
     * @param int $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty)
    {
        return $this->setData(self::TOTAL_QTY, $totalQty);   
    }

    /**
     * @return int
     */
    public function getQtyToShip()
    {
        return $this->getData(self::QTY_TO_SHIP);
    }

    /**
     * @param int $qtyToShip
     * @return $this
     */
    public function setQtyToShip($qtyToShip)
    {
        return $this->setData(self::QTY_TO_SHIP, $qtyToShip);   
    }

    /**
     * @return null|string
     */
    public function getShelfLocation()
    {
        return $this->getData(self::SHELF_LOCATION);
    }

    /**
     * @param string $shelfLocation
     * @return $this
     */
    public function setShelfLocation($shelfLocation)
    {
        return $this->setData(self::SHELF_LOCATION, $shelfLocation);   
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);   
    }

    /**
     * Updated at
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param int $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);  
    }
}