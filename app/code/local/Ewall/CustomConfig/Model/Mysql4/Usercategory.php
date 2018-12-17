<?php
class Ewall_CustomConfig_Model_Mysql4_Usercategory extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customconfig/usercategory", "id");
    }
}