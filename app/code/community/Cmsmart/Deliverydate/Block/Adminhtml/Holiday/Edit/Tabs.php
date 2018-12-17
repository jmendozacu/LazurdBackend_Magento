<?php

class Cmsmart_Deliverydate_Block_Adminhtml_Holiday_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('holiday_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('deliverydate')->__('Holiday Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('deliverydate')->__('Holiday Information'),
          'title'     => Mage::helper('deliverydate')->__('Holiday Information'),
          'content'   => $this->getLayout()->createBlock('deliverydate/adminhtml_holiday_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}