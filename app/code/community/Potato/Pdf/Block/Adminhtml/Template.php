<?php

class Potato_Pdf_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller     = 'adminhtml_template';
        $this->_blockGroup     = 'po_pdf';
        $this->_headerText     = $this->__('Templates');
        $this->_addButtonLabel = $this->__('Add Template');
        return parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}