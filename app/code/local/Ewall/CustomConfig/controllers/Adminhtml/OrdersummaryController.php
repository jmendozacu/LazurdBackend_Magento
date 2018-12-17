<?php
class Ewall_CustomConfig_Adminhtml_OrdersummaryController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout();
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Order Summary Reports'));
        $this->_initAction()
            ->_setActiveMenu('sales/ordersummary/index')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Order Summary Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_summary'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/ordersummary');
    }
}
