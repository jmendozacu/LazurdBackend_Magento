<?php
class Ewall_CustomConfig_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid{
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
         $this->getMassactionBlock()->removeItem('cancel_order');
         $this->getMassactionBlock()->removeItem('hold_order');
         $this->getMassactionBlock()->removeItem('unhold_order');
        return $this;
    }
}