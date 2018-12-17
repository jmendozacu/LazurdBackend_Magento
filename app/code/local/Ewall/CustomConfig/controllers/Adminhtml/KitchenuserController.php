<?php
class Ewall_CustomConfig_Adminhtml_KitchenuserController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout();
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Orders'))->_title($this->__('Sales'))->_title($this->__('Kitchecn User Reports'));
        $this->_initAction()
            ->_setActiveMenu('sales/kitchenusers/index')
            ->_addBreadcrumb(Mage::helper('customconfig')->__('Sales Report'), Mage::helper('customconfig')->__('Kitchecn User Report'));
        $this->_addContent($this->getLayout()->createBlock('customconfig/adminhtml_kitchenuser'));

        $this->renderLayout();

    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/kitchenusers');
    }
    
    public function exportKitchenuserCsvAction()
    {
        $fileName   = 'kitchenuser.csv';
        $content    = $this->getLayout()
            ->createBlock('customconfig/adminhtml_kitchenuser_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function exportKitchenuserExcelAction()
    {
        $fileName   = 'kitchenuser.xml';
        $content    = $this->getLayout()
            ->createBlock('customconfig/adminhtml_kitchenuser_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
