<?php
class Ewall_CustomConfig_Model_Resource_Product_Sold_Collection extends Ewall_CustomConfig_Model_Resource_Product_Collection 
{
    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
    }
    /**
     * Set Date range to collection
     *
     * @param int $from
     * @param int $to
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addOrderedQty($from, $to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()->where('order_items.store_id IN (?)', (array)$storeIds);
        }
        return $this;
    }

    /**
     * Add website product limitation
     *
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    protected function _productLimitationJoinWebsite()
    {
        $filters     = $this->_productLimitationFilters;
        $conditions  = array('product_website.product_id=e.entity_id');
        if (isset($filters['website_ids'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id IN(?)', $filters['website_ids']);

            $subQuery = $this->getConnection()->select()
                ->from(array('product_website' => $this->getTable('catalog/product_website')),
                    array('product_website.product_id')
                )
                ->where(join(' AND ', $conditions));
            $this->getSelect()->where('e.entity_id IN( '.$subQuery.' )');
        }

        return $this;
    }
}
