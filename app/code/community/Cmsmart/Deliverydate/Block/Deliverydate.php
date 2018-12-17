<?php
    class Cmsmart_Deliverydate_Block_Deliverydate extends Mage_Core_Block_Template
    {
        public function deliverytimegetdata()
        {
            $collection = Mage::getModel('deliverydate/deliverydate')->getCollection()->setOrder('id','asc');
            return $collection;
        }
        public function holidaygetdata()
        {
            $holiday = Mage::getModel('deliverydate/holiday')->getCollection()->setOrder('id','asc');
            return $holiday;
        }
        public function _prepareLayout()
        {
            return parent::_prepareLayout();
        }

        public function getDeliverydate()     
        { 
            if (!$this->hasData('deliverydate')) {
                $this->setData('deliverydate', Mage::registry('deliverydate'));
            }
            return $this->getData('deliverydate');

        }
        public function getDateFormat()
        {       
            return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) . ' ' . Mage::app()->getLocale()->getTimeStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }
}