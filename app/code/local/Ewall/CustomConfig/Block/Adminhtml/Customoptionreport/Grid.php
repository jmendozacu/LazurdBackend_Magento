<?php

class Ewall_CustomConfig_Block_Adminhtml_Customoptionreport_Grid extends Mage_Adminhtml_Block_Report_Grid 
{

    protected $_subReportSize = 0;
    protected $_exportVisibility = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts_with_custom_option_text_sold');
       
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

        $this->addColumn('increment_id',array(
            'header'    => Mage::helper('customconfig')->__('Order Id'),
            'index'     => 'increment_id',
        ));

        $this->addColumn('store_id', array(
            'header'    => Mage::helper('sales')->__('Branch'),
            'index'     => 'store_id',
            'type'      => 'store',
            'store_view'=> true,
            'display_deleted' => true,
        ));

        $this->addColumn('order_items_name', array(
            'header'    =>Mage::helper('customconfig')->__('Product Name'),
            'index'     =>'order_items_name'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('customconfig')->__('Product Sku'),
            'index'     =>'sku'
        ));

        $this->addColumn('created_at', array(
            'header'    =>Mage::helper('customconfig')->__('Ordered Date'),
            'index'     =>'created_at',
            'type'      => 'datetime',
            'width'     => '100px',
        ));

        $this->addColumn('shipping_delivery_date', array(
            'header'    =>Mage::helper('customconfig')->__('Delivery Date'),
            'index'     =>'shipping_delivery_date',
            'type'      => 'datetime',
            'width'     => '100px',
        ));

        $this->addColumn('text_custom_options_value', array(
            'header'    => Mage::helper('customconfig')->__('Message'),
            'index'     => 'text_custom_options_value',
            'renderer'  => 'customconfig/adminhtml_customoptionreport_renderer_customoptiontext'
        ));

        //$this->addExportType('*/*/exportKitchenuserCsv', Mage::helper('customconfig')->__('CSV'));
        //$this->addExportType('*/*/exportKitchenuserExcel', Mage::helper('customconfig')->__('Excel XML'));
        return parent::_prepareColumns();
    }
}
