<?php
    class Cmsmart_Deliverydate_Model_Config_Timeformat extends Mage_Core_Model_Config_Data
    {

        /**
        * Get possible sharing configuration options
        *
        * @return array
        */
        public function toOptionArray()
        {
            return array(
                'g:i a' => Mage::helper('deliverydate')->__('g:i a'),
                'H:i:s' => Mage::helper('deliverydate')->__('H:i:s')
            );
        }

    }
