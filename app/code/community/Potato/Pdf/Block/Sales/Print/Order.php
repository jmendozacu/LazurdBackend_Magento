<?php

class Potato_Pdf_Block_Sales_Print_Order extends Mage_Sales_Block_Order_Print
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}