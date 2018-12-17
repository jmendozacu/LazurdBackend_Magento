<?php
    class Cmsmart_Deliverydate_Model_Config_Dateformat extends Mage_Core_Model_Config_Data
    {

        /**
        * Get possible sharing configuration options
        *
        * @return array
        */
        public function toOptionArray()
        {
            return array(
                'd/M/Y' => Mage::helper('deliverydate')->__('d/M/Y'),
                'M/d/y' => Mage::helper('deliverydate')->__('M/d/y'),
                'd-M-Y' => Mage::helper('deliverydate')->__('d-M-Y'),
                'M-d-y' => Mage::helper('deliverydate')->__('M-d-y'),
                'm.d.y' => Mage::helper('deliverydate')->__('m.d.y'),
                'd.M.Y' => Mage::helper('deliverydate')->__('d.M.Y'),
                'M.d.y' => Mage::helper('deliverydate')->__('M.d.y'),
                'F j ,Y'=> Mage::helper('deliverydate')->__('F j ,Y'),
                'D M j' => Mage::helper('deliverydate')->__('D M j'),
                'Y-m-d' => Mage::helper('deliverydate')->__('Y-m-d')
            );
        }

    }
