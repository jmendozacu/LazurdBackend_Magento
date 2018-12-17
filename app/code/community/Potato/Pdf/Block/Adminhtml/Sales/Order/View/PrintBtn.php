<?php

class Potato_Pdf_Block_Adminhtml_Sales_Order_View_PrintBtn extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Add print button to order view page
     *
     * @param Mage_Core_Block_Abstract $block
     *
     * @return mixed
     */
    public function setParentBlock(Mage_Core_Block_Abstract $block)
    {
        $storeId = $block->getOrder()->getStoreId();
        if (Potato_Pdf_Helper_Config::isEnabled($storeId) &&
            Potato_Pdf_Helper_Config::isOrderEnabled($storeId) &&
            Potato_Pdf_Helper_Config::getOrderAdminTemplate($storeId)
        ) {
            $block->addButton('potato_order_pdf',
                array(
                    'label'   => $this->__('Print'),
                    'class'   => 'save',
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl($block->getOrder()->getId()) . '\')',
                )
            );
        }
        return parent::setParentBlock($block);
    }

    public function getPrintUrl($orderId)
    {
        $url_print = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/sales_order/print', array('id' => $orderId));
        $url_print = $url_print . "?XDEBUG_SESSION_START=PHPSTORM";
        return $url_print;
    }
}