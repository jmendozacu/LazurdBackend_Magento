<?php
 class Ewall_CustomConfig_Block_Adminhtml_Customoptionreport extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
	    $this->_controller = 'adminhtml_customoptionreport';
	    $this->_blockGroup = 'customconfig';
	    $this->_headerText = Mage::helper('customconfig')->__('Custom Option Report');
	    parent::__construct();
	    $this->_removeButton('add');
	}
}