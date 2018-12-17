<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'ShipmentController.php');

class Potato_Pdf_Adminhtml_Sales_ShipmentController extends Mage_Adminhtml_Sales_ShipmentController
{
    public function pdfshipmentsAction()
    {
        if (!Potato_Pdf_Helper_Adminhtml::isAdminShipmentPrintAllowed()) {
            $this->_forward('pdfshipments', 'sales_shipment', 'admin', $this->getRequest()->getParams());
            return parent::pdfshipmentsAction();
        }

        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $html = array();
            $collection = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
            ;
            foreach ($collection as $shipment) {
                $template = Mage::getModel('po_pdf/template')->load(
                    Potato_Pdf_Helper_Config::getShipmentAdminTemplate(
                        $shipment->getStoreId()
                    )
                );

                //prepare html
                $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getShipmentVariables($shipment), true);
            }
            return $this->_convertAndSendPdf($html, Potato_Pdf_Helper_Data::getFileName('shipments-'));
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