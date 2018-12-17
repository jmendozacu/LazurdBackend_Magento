<?php


class Ewall_Dirvermanagement_Block_Adminhtml_Driver extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_driver";
	$this->_blockGroup = "dirvermanagement";
	$this->_headerText = Mage::helper("dirvermanagement")->__("Driver Manager");
	$this->_addButtonLabel = Mage::helper("dirvermanagement")->__("Add New Item");
	parent::__construct();
	
	}

}