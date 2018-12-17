<?php

class Ewall_Dirvermanagement_Block_Adminhtml_Driver_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "dirvermanagement";
				$this->_controller = "adminhtml_driver";
				$this->_updateButton("save", "label", Mage::helper("dirvermanagement")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("dirvermanagement")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("dirvermanagement")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("driver_data") && Mage::registry("driver_data")->getId() ){

				    return Mage::helper("dirvermanagement")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("driver_data")->getId()));

				}
				else{

				     return Mage::helper("dirvermanagement")->__("Add Item");

				}
		}
}