<?php

class Potato_Pdf_Model_Source_Page_Orientation
{
    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => 'Portrait',
                'label' => Mage::helper('po_pdf')->__('Portrait')
            ),
            array(
                'value' => 'Landscape',
                'label' => Mage::helper('po_pdf')->__('Landscape')
            )
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Page Orientation'),
                'value' => $options
            );
        }
        return $options;
    }
}