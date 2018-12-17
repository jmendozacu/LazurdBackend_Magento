<?php
 class Ewall_CustomConfig_Block_Adminhtml_Orderstatussummary extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
	    $this->_controller = 'adminhtml_orderstatussummary';
	    $this->_blockGroup = 'customconfig';
	    $this->_headerText = Mage::helper('customconfig')->__('Order Status Report');
	    parent::__construct();
	    $this->_removeButton('add');
	}
}