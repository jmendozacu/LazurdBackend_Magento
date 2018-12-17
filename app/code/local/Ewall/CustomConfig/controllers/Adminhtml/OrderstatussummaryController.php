<?php
class Ewall_CustomConfig_Adminhtml_OrderstatussummaryController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout();
        return $this;
    }
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/orderstatussummary');
    }
    public function indexAction()
    {
        $this->_title($this->__('Orders'))->_title($this->__('Sales'))->_title($this->__('Order Status Reports'));
        $this->_initAction()
            ->_setActiveMenu('sales/orderstatussummary/index')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Order Status Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_orderstatussummary'));
        $this->renderLayout();

    }    
}
