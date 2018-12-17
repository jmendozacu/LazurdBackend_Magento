<?php

class Potato_Pdf_Model_Source_Page_Format
{
    const A4_VALUE = 'A4';
    const LETTER_VALUE = 'Letter';

    public function toOptionArray($withGroup = false)
    {
        $options = array(
            array(
                'value' => 'A4',
                'label' => Mage::helper('po_pdf')->__('A4')
            ),
            array(
                'value' => 'letter',
                'label' => Mage::helper('po_pdf')->__('Letter')
            )
        );
        if ($withGroup) {
            $options = array(
                'label' => Mage::helper('po_pdf')->__('Page Format'),
                'value' => $options
            );
        }
        return $options;
    }
}