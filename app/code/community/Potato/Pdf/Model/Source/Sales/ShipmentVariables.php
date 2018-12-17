<?php

class Potato_Pdf_Model_Source_Sales_ShipmentVariables
{
    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => '{{var shipment.increment_id}}',
                'label' => Mage::helper('po_pdf')->__('Increment Id')
            ),
            array(
                'value' => '{{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}',
                'label' => Mage::helper('po_pdf')->__('Items')
            ),
            array(
                'value' => "{{var shipment_formatted_date}}",
                'label' => Mage::helper('po_pdf')->__('Created At')
            ),
            array(
                'value' => '{{layout handle="po_pdf_sales_order_shipment_items"}}',
                'label' => Mage::helper('po_pdf')->__('Items')
            ),
            array(
                'value' => '{{block type="core/template" area="frontend" template="email/order/shipment/track.phtml" shipment=$shipment order=$order}}',
                'label' => Mage::helper('po_pdf')->__('Tracking Number')
            ),
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Shipment Information'),
                'value' => $options
            );
        }
        return $options;
    }
}