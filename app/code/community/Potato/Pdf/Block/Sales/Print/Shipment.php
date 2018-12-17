<?php

class Potato_Pdf_Block_Sales_Print_Shipment extends Mage_Sales_Block_Order_Print_Shipment
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}