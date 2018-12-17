<?php
    class Cmsmart_Deliverydate_Model_Config_Option extends Mage_Core_Model_Config_Data
    {
        const TYPE_SHIPPING_PAGE = 1;
        const TYPE_REVIEW_PAGE   = 2;

        /**
        * Get possible sharing configuration options
        *
        * @return array
        */
        public function toOptionArray()
        {
            return array(
                self::TYPE_SHIPPING_PAGE  => Mage::helper('deliverydate')->__('Shipping Method'),
                self::TYPE_REVIEW_PAGE => Mage::helper('deliverydate')->__('Review Page'),
            );
        }

    }
