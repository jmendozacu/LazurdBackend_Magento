<?php
class Ewall_CustomConfig_Adminhtml_CustomoptionreportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout();
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Orders'))->_title($this->__('Sales'))->_title($this->__('Custom Option Report With Message'));
        $this->_initAction()
            ->_setActiveMenu('sales/customoptionreport/index')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Custom Option Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_customoptionreport'));

        $this->renderLayout();

    }
	
	
	public function withoutmachineAction()
    {
        $this->_title($this->__('Orders'))->_title($this->__('Sales'))->_title($this->__('Custom Option Report Without Machine'));
        $this->_initAction()
            ->_setActiveMenu('sales/customoptionreport/index')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Custom Option Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_customoptionreport'));

        $this->renderLayout();

    }
	
	public function allreportAction()
    {
        $this->_title($this->__('Orders'))->_title($this->__('Sales'))->_title($this->__('Order Report'));
        $this->_initAction()
            ->_setActiveMenu('sales/customoptionreport/allreport')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Custom Option Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_customoptionreport'));

        $this->renderLayout();

    }
	

    protected function _isAllowed()
    {
        return true;
    }
}
