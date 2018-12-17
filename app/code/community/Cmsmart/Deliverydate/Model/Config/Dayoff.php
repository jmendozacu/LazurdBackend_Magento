<?php
    class Cmsmart_Deliverydate_Model_Config_Dayoff extends Mage_Core_Model_Config_Data
    {

        public function toOptionArray()
        {
            $test_array[7] = array(
                'value' => '',
                'label' => Mage::helper('deliverydate')->__('No Day'),
            );
            $test_array[0] = array(
                'value' => 0,
                'label' => Mage::helper('deliverydate')->__('Sunday'),
            );
            $test_array[1] = array(
                'value' => 1,
                'label' => Mage::helper('deliverydate')->__('Monday'),
            );
            $test_array[2] = array(
                'value' => 2,
                'label' => Mage::helper('deliverydate')->__('Tuesday'),
            );
            $test_array[3] = array(
                'value' => 3,
                'label' => Mage::helper('deliverydate')->__('Wedenesday'),
            );
            $test_array[4] = array(
                'value' => 4,
                'label' => Mage::helper('deliverydate')->__('Thursday'),
            );
            $test_array[5] = array(
                'value' => 5,
                'label' => Mage::helper('deliverydate')->__('Friday'),
            );
            $test_array[6] = array(
                'value' => 6,
                'label' => Mage::helper('deliverydate')->__('Saturday'),
            );
            
            return $test_array;
        }

    }
