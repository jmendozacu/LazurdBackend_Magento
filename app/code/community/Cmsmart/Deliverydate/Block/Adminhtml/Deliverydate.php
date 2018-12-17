<?php
class Cmsmart_Deliverydate_Block_Adminhtml_Deliverydate extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_deliverydate';
    $this->_blockGroup = 'deliverydate';
    $this->_headerText = Mage::helper('deliverydate')->__('Delivery Time Manager');
    $this->_addButtonLabel = Mage::helper('deliverydate')->__('Add Delivery Time');
    parent::__construct();
  }
}