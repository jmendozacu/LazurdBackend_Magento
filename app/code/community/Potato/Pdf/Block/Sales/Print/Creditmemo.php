<?php

class Potato_Pdf_Block_Sales_Print_Creditmemo extends Mage_Sales_Block_Order_Print_Creditmemo
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}