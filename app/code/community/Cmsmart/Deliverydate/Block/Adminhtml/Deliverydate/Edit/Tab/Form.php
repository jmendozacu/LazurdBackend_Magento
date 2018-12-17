<?php

class Cmsmart_Deliverydate_Block_Adminhtml_Deliverydate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('deliverydate_form', array('legend'=>Mage::helper('deliverydate')->__('Delivery Time information')));
     
      $fieldset->addField('fromtime', 'text', array(
          'label'     => Mage::helper('deliverydate')->__('From Time'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'fromtime',
          'note'      => 'hours:minutes:seconds'
      ));

      $fieldset->addField('totime', 'text', array(
          'label'     => Mage::helper('deliverydate')->__('To Time'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'totime',
          'note'      => 'hours:minutes:seconds'
	  ));

     
     
      if ( Mage::getSingleton('adminhtml/session')->getDeliverydateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDeliverydateData());
          Mage::getSingleton('adminhtml/session')->setDeliverydateData(null);
      } elseif ( Mage::registry('deliverydate_data') ) {
          $form->setValues(Mage::registry('deliverydate_data')->getData());
      }
      return parent::_prepareForm();
  }
}