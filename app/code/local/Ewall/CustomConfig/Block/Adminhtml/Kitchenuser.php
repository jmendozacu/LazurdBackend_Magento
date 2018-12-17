<?php
 class Ewall_CustomConfig_Block_Adminhtml_Kitchenuser extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
	    $this->_controller = 'adminhtml_kitchenuser';
	    $this->_blockGroup = 'customconfig';
	    $this->_headerText = Mage::helper('customconfig')->__('Kitchenuser Report');
	    parent::__construct();
	    $this->_removeButton('add');
	}
}