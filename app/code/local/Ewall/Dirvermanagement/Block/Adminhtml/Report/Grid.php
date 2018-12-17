<?php

class Ewall_Dirvermanagement_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId("reportGrid");
		$this->setDefaultSort("id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
		$this->setPagerVisibility(false);
		$this->setFilterVisibility(false);
	}

    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
		$data = $this->getRequest()->getPost();
		$date_from = $data['form-date']." 00:00:00";
		$date_to = $data['to-date']." 23:59:59";
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection =  $collection->addFieldToFilter('created_at' , array('from'=> $date_from,'to'=>  $date_to));
        
        $collection->addFieldToFilter('order_status' , array(array('eq' => 'delivered'), array('eq' => 'returned')));
        if($data['delivery']){
        	$collection->addFieldToFilter('order_status' , ['neq' => 'delivered']);
        }
        $this->setCollection($collection);
       // return parent::_prepareCollection();
    }

	protected function _prepareColumns()
	{

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order Number'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));
        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Customer Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('shipping_arrival_date', array(
            'header' => Mage::helper('sales')->__('Delivery Date'),
            'index' => 'shipping_arrival_date',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('order_status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'order_status',
            'type'  => 'text',
            'renderer'         => 'dirvermanagement/adminhtml_order_renderer_orderstatus',
        ));

			return parent::_prepareColumns();
	}


}