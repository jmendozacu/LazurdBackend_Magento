<?php

class Cmsmart_Deliverydate_Block_Adminhtml_Deliverydate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('deliverydateGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('deliverydate/deliverydate')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('deliverydate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('fromtime', array(
          'header'    => Mage::helper('deliverydate')->__('From Time'),
          'align'     =>'left',
          'index'     => 'fromtime',
      ));
       $this->addColumn('totime', array(
          'header'    => Mage::helper('deliverydate')->__('To Time'),
          'align'     =>'left',
          'index'     => 'totime',
      ));

	
		
		$this->addExportType('*/*/exportCsv', Mage::helper('deliverydate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('deliverydate')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('deliverydate');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('deliverydate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('deliverydate')->__('Are you sure?')
        ));        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}