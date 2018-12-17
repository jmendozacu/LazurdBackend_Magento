<?php

class Cmsmart_Deliverydate_Block_Adminhtml_Holiday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('holiday_form', array('legend' => Mage::helper('deliverydate')->__('Holiday information')));

        $fieldset->addField('year', 'text', array(
            'label' => Mage::helper('deliverydate')->__('Year'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'year',
        ));
        $fieldset->addField('month', 'select', array(
            'label' => Mage::helper('deliverydate')->__('Month'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'month',
            'values' => array(
                '1' => '1', 
                '2' => '2', 
                '3' => '3', 
                '4' => '4', 
                '5' => '5', 
                '6' => '6', 
                '7' => '7', 
                '8' => '8', 
                '9'  =>'9', 
                '10' =>'10',
                '11' =>'11',
                '12' =>'12',                         
            ),
        ));
        $fieldset->addField('day', 'select', array(
            'label' => Mage::helper('deliverydate')->__('Day'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'day',
            'values' => array(
                '1' => '1', 
                '2' => '2', 
                '3' => '3', 
                '4' => '4', 
                '5' => '5', 
                '6' => '6', 
                '7' => '7', 
                '8' => '8', 
                '9'  =>'9', 
                '10' =>'10',
                '11' =>'11',
                '12' =>'12',
                '13' =>'13',
                '14' =>'14',
                '15' =>'15',
                '16' =>'16',
                '17' =>'17',
                '18' =>'18',
                '19' =>'19',
                '20' =>'20',
                '21' =>'21',
                '22' =>'22',
                '23' =>'23',
                '24' =>'24',
                '25' =>'25',
                '26' =>'26',
                '27' =>'27',
                '28' =>'28',
                '29' =>'29',
                '30' =>'30',
                '31' =>'31',               
            ),
        ));
        $fieldset->addField('description', 'text', array(
            'label' => Mage::helper('deliverydate')->__('Description'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'description',
        ));

        if (Mage::getSingleton('adminhtml/session')->getDeliverydateData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDeliverydateData());
            Mage::getSingleton('adminhtml/session')->setDeliverydateData(null);
        } elseif (Mage::registry('holiday_data')) {
            $form->setValues(Mage::registry('holiday_data')->getData());
        }
        return parent::_prepareForm();
    }

}