<?php

class Cmsmart_Deliverydate_Block_Adminhtml_Holiday_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'deliverydate';
        $this->_controller = 'adminhtml_holiday';
        
        $this->_updateButton('save', 'label', Mage::helper('deliverydate')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('deliverydate')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('deliverydate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'holiday_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'holiday_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('holiday_data') && Mage::registry('holiday_data')->getId() ) {
            return Mage::helper('deliverydate')->__("Edit Holiday", $this->htmlEscape(Mage::registry('holiday_data')->getTitle()));
        } else {
            return Mage::helper('deliverydate')->__('Add Holiday');
        }
    }
}