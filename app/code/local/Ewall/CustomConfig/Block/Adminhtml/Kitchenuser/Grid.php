<?php

class Ewall_CustomConfig_Block_Adminhtml_Kitchenuser_Grid extends Mage_Adminhtml_Block_Report_Grid 
{

    protected $_subReportSize = 0;
    protected $_exportVisibility = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProductsKitchensold');
       
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('reports/product_sold_collection');
        return $this;
    }

    public function getCountTotals()
    {
            return false;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('image', array(
              'header'    => Mage::helper('customconfig')->__('Image'),
              'align'     => 'left',
              'index'     => 'image',
              'width'     => '100px',
              'renderer'  => 'customconfig/adminhtml_kitchenuser_renderer_image'
        ));

        $this->addColumn('sku',array(
                'header'=> Mage::helper('customconfig')->__('Sku'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('customconfig')->__('Product Name'),
            'index'     =>'order_items_name'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('customconfig')->__('Quantity Ordered'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addColumn('custom_options', array(
              'header'    => Mage::helper('customconfig')->__('Custom Options'),
              'align'     =>'left',
              'index'     => 'custom_options',
              'renderer'  => 'customconfig/adminhtml_kitchenuser_renderer_customoption'
        ));

        $this->addExportType('*/*/exportKitchenuserCsv', Mage::helper('customconfig')->__('CSV'));
        $this->addExportType('*/*/exportKitchenuserExcel', Mage::helper('customconfig')->__('Excel XML'));
        return parent::_prepareColumns();
    }
}
