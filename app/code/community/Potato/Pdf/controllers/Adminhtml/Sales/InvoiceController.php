<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'InvoiceController.php');

class Potato_Pdf_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Sales_InvoiceController
{
    public function pdfinvoicesAction()
    {
        if (!Potato_Pdf_Helper_Adminhtml::isAdminInvoicePrintAllowed()) {
            return parent::pdfinvoicesAction();
        }

        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (!empty($invoicesIds)) {
            $html = array();
            $collection = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $invoicesIds))
            ;
            foreach ($collection as $invoice) {
                $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getInvoiceAdminTemplate($invoice->getStoreId()));

                //prepare html
                $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getInvoiceVariables($invoice), true);
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('invoices-'));
        }
        $this->_redirect('*/*/');
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