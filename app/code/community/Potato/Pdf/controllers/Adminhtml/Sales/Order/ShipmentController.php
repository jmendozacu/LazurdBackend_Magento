<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'Order' . DS . 'ShipmentController.php');

class Potato_Pdf_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{
    public function printAction()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load($this->getRequest()->getParam('invoice_id', null));
        if (!Potato_Pdf_Helper_Adminhtml::isAdminShipmentPrintAllowed($shipment->getStoreId())) {
            return parent::printAction();
        }
        $template = Mage::getModel('po_pdf/template')->load(
            Potato_Pdf_Helper_Config::getShipmentAdminTemplate($shipment->getStoreId())
        );

        //prepare html
        $html = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getShipmentVariables($shipment), true);

        return $this->_convertAndSendPdf(array($html), Potato_Pdf_Helper_Data::getFileName('shipment-'), $shipment->getStoreId());
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