<?php

require_once 'Mage/Adminhtml/controllers/System/VariableController.php';
class Potato_Pdf_Adminhtml_Potato_Pdf_Template_VariableController extends Mage_Adminhtml_System_VariableController
{
    public function wysiwygPluginAction()
    {
        $variables = array(
            Mage::getModel('core/variable')->getVariablesOptionArray(true),
            Mage::getModel('core/source_email_variables')->toOptionArray(true),
            Mage::getModel('po_pdf/source_sales_OrderVariables')->toOptionArray(true),
            Mage::getModel('po_pdf/source_sales_InvoiceVariables')->toOptionArray(true),
            Mage::getModel('po_pdf/source_sales_ShipmentVariables')->toOptionArray(true),
            Mage::getModel('po_pdf/source_sales_CreditmemoVariables')->toOptionArray(true),
        );
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }

    protected function _isAllowed()
    {
        return true;
    }
}