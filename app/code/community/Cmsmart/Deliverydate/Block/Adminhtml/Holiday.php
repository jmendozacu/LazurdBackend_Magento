<?php
class Cmsmart_Deliverydate_Block_Adminhtml_Holiday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
   $this->_controller = 'adminhtml_holiday';
    $this->_blockGroup = 'deliverydate';
    $this->_headerText = 'Holiday Manager';
    $this->_addButtonLabel ='Add Holiday';
    parent::__construct();
  }
}