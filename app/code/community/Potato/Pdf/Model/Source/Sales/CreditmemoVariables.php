<?php

class Potato_Pdf_Model_Source_Sales_CreditmemoVariables
{
    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => '{{var creditmemo.increment_id}}',
                'label' => Mage::helper('po_pdf')->__('Increment Id')
            ),
            array(
                'value' => "{{var creditmemo_formatted_date}}",
                'label' => Mage::helper('po_pdf')->__('Created At')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_creditmemo_items"}}',
                'label' => Mage::helper('po_pdf')->__('Items')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_creditmemo_totals"}}',
                'label' => Mage::helper('po_pdf')->__('Totals')
            )
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Credit Memo Information'),
                'value' => $options
            );
        }
        return $options;
    }
}