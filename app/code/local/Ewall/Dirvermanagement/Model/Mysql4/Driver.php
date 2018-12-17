<?php
class Ewall_Dirvermanagement_Model_Mysql4_Driver extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("dirvermanagement/driver", "id");
    }
}