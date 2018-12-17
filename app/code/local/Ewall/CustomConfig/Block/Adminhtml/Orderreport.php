<?php
 class Ewall_CustomConfig_Block_Adminhtml_Orderreport extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
	    $this->_controller = 'adminhtml_orderreport';
	    $this->_blockGroup = 'customconfig';
	    $this->_headerText = Mage::helper('customconfig')->__('Order Report');
	    parent::__construct();
	    $this->_removeButton('add');
	}
}

