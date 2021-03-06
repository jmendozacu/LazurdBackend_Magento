<?php

    class Cmsmart_Deliverydate_Model_Holiday extends Mage_Core_Model_Abstract
    {
        const TYPE_SHIPPING_METHOD    = 1;
        const TYPE_REVIEW_PAGE    = 2;
        public function _construct()
        {
            parent::_construct();
            $this->_init('deliverydate/holiday');
        }

        public function toOptionArray()
        {
            return array(
                self::TYPE_SHIPPING_METHOD => Mage::helper('adminhtml')->__('Shipping Method'),
                self::TYPE_REVIEW_PAGE => Mage::helper('adminhtml')->__('Order Review Page')
            );
        }
}