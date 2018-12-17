<?php

class Potato_Pdf_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'po_pdf';
        $this->_controller = 'adminhtml_template';
        $this->_formScripts[] = "
            function saveAndContinueEdit(url) {
               editForm.submit(url);
            };
            var PdfDefaultTemplate = new PdfDefaultTemplateClass('" .
            $this->getUrl('adminhtml/potato_pdf_template/defaultTemplate') .
            "', $('edit_form'));
        ";
        parent::__construct();
    }

    public function getHeaderText()
    {
        $title = $this->__('New Template');
        $template = Mage::registry('current_template');
        if (null !== $template->getId()) {
            $title = $this->__(
                'Edit Template "%s"',
                $template->getTitle()
            );
        }
        return $title;
    }

    protected function _prepareLayout()
    {
        $this->_addButton(
            'save_and_continue',
            array(
                'label'   => $this->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
                'class'   => 'save'
            ), 10
        );
        parent::_prepareLayout();
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            array(
                '_current' => true,
                'back'     => 'edit'
            )
        );
    }
}