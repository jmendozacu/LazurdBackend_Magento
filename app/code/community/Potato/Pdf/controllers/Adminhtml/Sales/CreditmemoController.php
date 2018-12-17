<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'CreditmemoController.php');

class Potato_Pdf_Adminhtml_Sales_CreditmemoController extends Mage_Adminhtml_Sales_CreditmemoController
{
    public function pdfcreditmemosAction()
    {
        if (!Potato_Pdf_Helper_Adminhtml::isAdminCreditMemoPrintAllowed()) {
            return parent::pdfcreditmemosAction();
        }

        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $collection = Mage::getResourceModel('sales/order_creditmemo_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
            ;
            foreach ($collection as $creditmemo) {
                $template = Mage::getModel('po_pdf/template')->load(
                    Potato_Pdf_Helper_Config::getCreditMemoAdminTemplate(
                        $creditmemo->getStoreId()
                    )
                );

                //prepare html
                $html[] = $template->getProcessedTemplate(Mage::helper('po_pdf/sales')->getCreditMemoVariables($creditmemo), true);
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