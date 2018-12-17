<?php

class Potato_Pdf_Model_Source_Sales_InvoiceVariables
{
    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => '{{var invoice.increment_id}}',
                'label' => Mage::helper('po_pdf')->__('Increment Id')
            ),
            array(
                'value' => "{{var invoice_formatted_date}}",
                'label' => Mage::helper('po_pdf')->__('Created At')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_invoice_items"}}',
                'label' => Mage::helper('po_pdf')->__('Items')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_invoice_totals"}}',
                'label' => Mage::helper('po_pdf')->__('Totals')
            ),
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Invoice Information'),
                'value' => $options
            );
        }
        return $options;
    }
}