<?php
    class Cmsmart_Deliverydate_Block_Holiday extends Mage_Core_Block_Template
    {
        public function _prepareLayout()
        {
            return parent::_prepareLayout();
        }

        public function getDeliverydate()     
        { 
            if (!$this->hasData('holiday')) {
                $this->setData('holiday', Mage::registry('holiday'));
            }
            return $this->getData('holiday');

        }
        public function getDateFormat()
        {       
            return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) . ' ' . Mage::app()->getLocale()->getTimeStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }
}