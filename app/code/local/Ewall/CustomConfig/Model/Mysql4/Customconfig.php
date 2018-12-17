<?php
class Ewall_CustomConfig_Model_Mysql4_Customconfig extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customconfig/customconfig", "id");
    }
}