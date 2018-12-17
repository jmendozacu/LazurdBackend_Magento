<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'Order' . DS . 'CreditmemoController.php');

class Potato_Pdf_Adminhtml_Sales_Order_CreditmemoController extends Mage_Adminhtml_Sales_Order_CreditmemoController
{
    public function printAction()
    {
        $creditMemo = Mage::getModel('sales/order_creditmemo')->load($this->getRequest()->getParam('creditmemo_id', null));
        if (!Potato_Pdf_Helper_Adminhtml::isAdminCreditMemoPrintAllowed($creditMemo->getStoreId())) {
            return parent::printAction();
        }
        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getCreditMemoAdminTemplate($creditMemo->getStoreId()));

        //prepare html
        $html = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getCreditMemoVariables($creditMemo), true);

        return $this->_convertAndSendPdf(array($html), Potato_Pdf_Helper_Data::getFileName('creditmemo-'), $creditMemo->getStoreId());
    }

    protected function _errorHandle()
    {
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('po_pdf')->__('An error occurred while file creating')
        );
        $this->_redirectReferer();
        return;
    }

    /**
     * @param array $html
     * @param string $filename
     * @param mixed $store
     */
    protected function _convertAndSendPdf($html, $filename, $store=null)
    {
        try {
            //convert
            $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf($html, $store);
        } catch (Exception $e) {
            return $this->_errorHandle();
        }
        if (!$pdfContent) {
            return $this->_errorHandle();
        }
        return $this->_prepareDownloadResponse($filename, $pdfContent, 'application/pdf');
    }
}