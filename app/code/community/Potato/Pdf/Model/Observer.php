<?php

class Potato_Pdf_Model_Observer
{
    public function addMassOrderPrintAction(Varien_Event_Observer $observer)
    {
        if (!Potato_Pdf_Helper_Config::isEnabled() ||
            !Potato_Pdf_Helper_Config::isOrderEnabled() ||
            !Potato_Pdf_Helper_Config::getOrderAdminTemplate()
        ) {
            return $this;
        }

        $block = $observer->getBlock();
        if (!$block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction ||
            !$block->getParentBlock() ||
            $block->getParentBlock()->getNameInLayout() != 'sales_order.grid' ||
            !$block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Grid
        ) {
            return $this;
        }
        $block->addItem('po_pdf_mass_order_print',
            array(
                'label' => Mage::helper('po_pdf')->__('Print Orders'),
                'url'   => Mage_Adminhtml_Helper_Data::getUrl('adminhtml/sales_order/pdforders'),
            )
        );
        return $this;
    }
}