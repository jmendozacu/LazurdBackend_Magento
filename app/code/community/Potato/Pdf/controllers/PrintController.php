<?php

require_once 'Mage/Sales/controllers/OrderController.php';
class Potato_Pdf_PrintController extends Mage_Sales_OrderController
{
    /**
     * customer print order action
     */
    public function orderAction()
    {
        if (!Potato_Pdf_Helper_Config::isEnabled() ||
            !Potato_Pdf_Helper_Config::isOrderEnabled() ||
            !Potato_Pdf_Helper_Config::getOrderCustomerTemplate()
        ) {
            $this->_forward('print', 'order', 'sales', $this->getRequest()->getParams());
            return;
        }
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id', null));
        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getOrderCustomerTemplate());

        //prepare html
        $html = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getOrderVariables($order));

        try {
            //convert
            $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf(array($html), $order->getStoreId());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('po_pdf')->__($e->getMessage())
            );
            $this->_redirectReferer();
            return;
        }

        if (!$pdfContent) {
            return $this->_errorHandle();
        }
        return $this->_prepareSafeDownloadResponse(Potato_Pdf_Helper_Data::getFileName('customer-order-'),
            $pdfContent, 'application/pdf'
        );
    }

    protected function _errorHandle()
    {
        Mage::getSingleton('customer/session')->addError(
            Mage::helper('po_pdf')->__('An error occurred while file creating')
        );
        $this->_redirectReferer();
        return;
    }

    /**
     * customer print invoice(s) action
     */
    public function invoiceAction()
    {
        if (!Potato_Pdf_Helper_Config::isEnabled() ||
            !Potato_Pdf_Helper_Config::isInvoiceEnabled() ||
            !Potato_Pdf_Helper_Config::getInvoiceCustomerTemplate()
        ) {
            $this->_forward('printInvoice', 'order', 'sales', $this->getRequest()->getParams());
            return;
        }
        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getInvoiceCustomerTemplate());
        $htmlList = array();
        $salesVariablesHelper = Mage::helper('po_pdf/sales');
        if ($this->getRequest()->getParam('order_id', false)) {
            //print all invoices
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id', null));
            foreach ($order->getInvoiceCollection() as $invoice) {
                //prepare html
                array_push(
                    $htmlList,
                    $template->getProcessedTemplate($salesVariablesHelper->getInvoiceVariables($invoice))
                );
            }
        } else {
            //print specific invoice
            $invoice = Mage::getModel('sales/order_invoice')->load($this->getRequest()->getParam('invoice_id', null));

            //prepare html
            array_push(
                $htmlList,
                $template->getProcessedTemplate($salesVariablesHelper->getInvoiceVariables($invoice))
            );
        }
        try {
            //convert
            $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf($htmlList, $invoice->getStoreId());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('po_pdf')->__($e->getMessage())
            );
            $this->_redirectReferer();
            return;
        }

        if (!$pdfContent) {
            return $this->_errorHandle();
        }
        return $this->_prepareSafeDownloadResponse(Potato_Pdf_Helper_Data::getFileName('customer-invoice-'),
            $pdfContent, 'application/pdf'
        );
    }

    /**
     * customer print shipment(s) action
     */
    public function shipmentAction()
    {
        if (!Potato_Pdf_Helper_Config::isEnabled() ||
            !Potato_Pdf_Helper_Config::isShipmentEnabled() ||
            !Potato_Pdf_Helper_Config::getShipmentCustomerTemplate()
        ) {
            $this->_forward('printShipment', 'order', 'sales', $this->getRequest()->getParams());
            return;
        }
        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getShipmentCustomerTemplate());
        $htmlList = array();
        $salesVariablesHelper = Mage::helper('po_pdf/sales');
        if ($this->getRequest()->getParam('order_id', false)) {
            //print all shipments
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id', null));
            foreach ($order->getShipmentsCollection() as $shipment) {
                //prepare html
                array_push(
                    $htmlList,
                    $template->getProcessedTemplate($salesVariablesHelper->getShipmentVariables($shipment))
                );
            }
        } else {
            //print specific shipment
            $shipment = Mage::getModel('sales/order_shipment')->load($this->getRequest()->getParam('shipment_id', null));

            //prepare html
            array_push(
                $htmlList,
                $template->getProcessedTemplate($salesVariablesHelper->getShipmentVariables($shipment))
            );
        }

        try {
            //convert
            $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf($htmlList, $shipment->getStoreId());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('po_pdf')->__($e->getMessage())
            );
            $this->_redirectReferer();
            return;
        }

        if (!$pdfContent) {
            return $this->_errorHandle();
        }
        return $this->_prepareSafeDownloadResponse(Potato_Pdf_Helper_Data::getFileName('customer-shipment-'),
            $pdfContent, 'application/pdf'
        );
    }

    public function creditMemoAction()
    {
        if (!Potato_Pdf_Helper_Config::isEnabled() ||
            !Potato_Pdf_Helper_Config::isCreditMemoEnabled() ||
            !Potato_Pdf_Helper_Config::getCreditMemoCustomerTemplate()
        ) {
            $this->_forward('printCreditmemo', 'order', 'sales', $this->getRequest()->getParams());
            return;
        }
        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getCreditMemoCustomerTemplate());
        $htmlList = array();
        $salesVariablesHelper = Mage::helper('po_pdf/sales');
        if ($this->getRequest()->getParam('order_id', false)) {
            //print all refunds
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id', null));
            foreach ($order->getCreditmemosCollection() as $credit) {
                //prepare html
                array_push(
                    $htmlList,
                    $template->getProcessedTemplate($salesVariablesHelper->getCreditMemoVariables($credit))
                );
            }
        } else {
            //print specific refund
            $credit = Mage::getModel('sales/order_creditmemo')->load($this->getRequest()->getParam('creditmemo_id', null));

            //prepare html
            array_push(
                $htmlList,
                $template->getProcessedTemplate($salesVariablesHelper->getCreditMemoVariables($credit))
            );
        }

        try {
            //convert
            $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf($htmlList, $credit->getStoreId());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('po_pdf')->__($e->getMessage())
            );
            $this->_redirectReferer();
            return;
        }

        if (!$pdfContent) {
            return $this->_errorHandle();
        }
        return $this->_prepareSafeDownloadResponse(Potato_Pdf_Helper_Data::getFileName('customer-creditmemo-'),
            $pdfContent, 'application/pdf'
        );
    }

    protected function _prepareSafeDownloadResponse($fileName, $content, $contentType = 'application/octet-stream',
        $contentLength = null
    ) {
        if (method_exists($this, '_prepareDownloadResponse')) {
            return parent::_prepareDownloadResponse($fileName, $content, $contentType, $contentLength);
        }
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Last-Modified', date('r'));
        $this->getResponse()->setBody($content);
        return $this;
    }
}