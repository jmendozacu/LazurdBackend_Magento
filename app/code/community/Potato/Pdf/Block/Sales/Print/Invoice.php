<?php

class Potato_Pdf_Block_Sales_Print_Invoice extends Mage_Sales_Block_Order_Print_Invoice
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}