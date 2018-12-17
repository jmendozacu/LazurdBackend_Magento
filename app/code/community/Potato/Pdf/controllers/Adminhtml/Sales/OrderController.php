<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'OrderController.php');

class Potato_Pdf_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController 
{
    public function printAction()
    {
        //load order
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id', null));

        //load template
        $template = Mage::getModel('po_pdf/template')->load(
            Potato_Pdf_Helper_Config::getOrderAdminTemplate($order->getStoreId())
        );

        //prepare html
        $html = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getOrderVariables($order), true);

        return $this->_convertAndSendPdf(array($html), Potato_Pdf_Helper_Data::getFileName('order-'), $order->getStoreId());
    }

    /**
     * Print documents for selected orders
     */
    public function pdfordersAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            $html = array();
            $collection = Mage::getModel('sales/order')->getCollection()->addFieldToSearchFilter('entity_id', $orderIds);
            foreach ($collection as $order) {
                $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getOrderAdminTemplate($order->getStoreId()));

                //prepare html
                $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getOrderVariables($order), true);
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('orders-'));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction()
    {
        // Islam Elgarhy PDF 2018

        // Call Order pdf as if no invoice attached Islam Elgarhy 2018
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            $html = array();
            $collection = Mage::getModel('sales/order')->getCollection()->addFieldToSearchFilter('entity_id', $orderIds);
            foreach ($collection as $order) {
                $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getOrderAdminTemplate($order->getStoreId()));

                //prepare html
                $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getOrderVariables($order), true);
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('invoices-'));
        }
        $this->_redirect('*/*/');


        /*if (!Potato_Pdf_Helper_Adminhtml::isAdminInvoicePrintAllowed()) {
            return parent::pdfinvoicesAction();
        }
   
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        if (!empty($orderIds)) {
            $html = array();
            foreach ($orderIds as $orderId) {
                foreach (Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId) as $invoice) {
                    $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getInvoiceAdminTemplate($invoice->getStoreId()));

                    //prepare html
                    $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getInvoiceVariables($invoice), true);
                }
            } 
            //foreach ($orderIds as $orderId) {
            //        $invoice = Mage::getModel('sales/order')->load($orderId);
            //        Mage::log( $invoice ,null,'loogogggg.log');
            //        $template = Mage::getModel('po_pdf/template')->load(Potato_Pdf_Helper_Config::getInvoiceAdminTemplate($invoice->getStoreId()));
                    
            //        Mage::log( $template,null,'loogogggg.log');
            //        //prepare html
            //        $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getInvoiceVariables($invoice), true);
              
            //}
           
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('invoices-'));
        }
        */
        //$this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction()
    {
        if (!Potato_Pdf_Helper_Adminhtml::isAdminShipmentPrintAllowed()) {
            return parent::pdfshipmentsAction();
        }

        $orderIds = $this->getRequest()->getPost('order_ids', array());
        if (!empty($orderIds)) {
            $html = array();
            foreach ($orderIds as $orderId) {
                foreach (Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId) as $shipment) {
                    $template = Mage::getModel('po_pdf/template')->load(
                        Potato_Pdf_Helper_Config::getShipmentAdminTemplate(
                            $shipment->getStoreId()
                        )
                    );

                    //prepare html
                    $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getShipmentVariables($shipment), true);
                }
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('shipments-'));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print creditmemos for selected orders
     */
    public function pdfcreditmemosAction()
    {
        if (!Potato_Pdf_Helper_Adminhtml::isAdminCreditMemoPrintAllowed()) {
            return parent::pdfcreditmemosAction();
        }

        $orderIds = $this->getRequest()->getPost('order_ids', array());
        if (!empty($orderIds)) {
            $html = array();
            foreach ($orderIds as $orderId) {
                foreach (Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($orderId) as $creditmemo) {
                    $template = Mage::getModel('po_pdf/template')->load(
                        Potato_Pdf_Helper_Config::getCreditMemoAdminTemplate(
                            $creditmemo->getStoreId()
                        )
                    );

                    //prepare html
                    $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getCreditMemoVariables($creditmemo), true);
                }
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('creditmemos-'));
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