<?php

class Potato_Pdf_Model_Resource_Template_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('po_pdf/template');
    }

    protected function _toOptionHash($valueField = 'id', $labelField = 'title')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }
}