<?php

class Potato_Pdf_Model_Source_Sales_OrderVariables
{
    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => '{{htmlescape var=$order.getCustomerName()}}',
                'label' => Mage::helper('po_pdf')->__('Customer Name')
            ),
            array(
                'value' => '{{var order.increment_id}}',
                'label' => Mage::helper('po_pdf')->__('Increment Id')
            ),
            array(
                'value' => "{{var order.getCreatedAtFormated('long')}}",
                'label' => Mage::helper('po_pdf')->__('Created At')
            ),
            array(
                'value' => "{{var order.getBillingAddress().format('html')}}",
                'label' => Mage::helper('po_pdf')->__('Billing Address')
            ),
            array(
                'value' => "{{var payment_html}}",
                'label' => Mage::helper('po_pdf')->__('Payment Information')
            ),
            array(
                'value' => "{{depend order.getIsNotVirtual()}} {{/depend}}",
                'label' => Mage::helper('po_pdf')->__('Is Virtual Condition')
            ),
            array(
                'value' => "{{var order.getShippingAddress().format('html')}}",
                'label' => Mage::helper('po_pdf')->__('Shipping Address')
            ),
            array(
                'value' => "{{var order.getShippingDescription()}}",
                'label' => Mage::helper('po_pdf')->__('Shipping Description')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_items"}}',
                'label' => Mage::helper('po_pdf')->__('Items')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_totals"}}',
                'label' => Mage::helper('po_pdf')->__('Totals')
            )
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Order Information'),
                'value' => $options
            );
        }
        return $options;
    }
}