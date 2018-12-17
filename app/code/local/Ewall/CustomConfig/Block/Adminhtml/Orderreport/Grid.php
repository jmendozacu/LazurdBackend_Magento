<?php

class Ewall_CustomConfig_Block_Adminhtml_Orderreport_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{

    protected $_subReportSize = 0;
    protected $_exportVisibility = false;

 
	
	public function __construct()
    {
		
        parent::__construct();
 		$this->setId('sales_order_grid_report');       
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DES');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('order_report_filter');
 		$this->setUseAjax(false);
       
    }
	
	 
	protected function _getCollectionClass()
    {
        return 'sales/order_item_collection';
    }
 
    protected function _prepareCollection()
    {
         $collection = Mage::getResourceModel($this->_getCollectionClass())
		// $collection = Mage::getResourceModel('sales/order_collection')
        ->addAttributeToSelect('*');  
 		$collection->getSelect()->join('sales_flat_order', 'main_table.order_id = sales_flat_order.entity_id',array('increment_id'))
		->columns("if(sales_flat_order.shipping_delivery_date is null,main_table.created_at,sales_flat_order.shipping_delivery_date) as shipping_delivery_date");
		//$collection->getSelect("if(sales_flat_order.shipping_delivery_date is null,main_table.created_at,sales_flat_order.shipping_delivery_date) as shipping_delivery_date");
		
		$collection->getSelect();
   /* 
			->join(
        array("t1" => 'sales_flat_order'),
        "main_table.order_id = t1.entity_id",
        array("shipping_delivery_date" => "t1.shipping_delivery_date","increment_id"=>"t1.increment_id")
    )
    ->where("t1.shipping_delivery_date is null");
		
		//$collection->getSelect()->where("COALESCE(NULLIF(sales_flat_order.shipping_delivery_date, main_table.created_at)) IS NULL");
		
		//SELECT IFNULL( (SELECT field1 FROM table WHERE id = 123 LIMIT 1) ,'not found');
		/*  
		$collection->getSelect->order(
			new Zend_Db_Expr(
		   "CASE WHEN 'created_at' IS NULL THEN 'created_at'
			WHEN 'created_at' = '' THEN 'created_at'

			ELSE 5 END"
			)
		); */
		
		//$collection->getSelect("ifnull('shipping_delivery_date', '123')");
		
		
		
		// echo $collection->getSelect();
		
        $this->setCollection($collection);
        
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
		
		$this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Order Id'),
            'index' => 'increment_id',
            'width' => '100px',
		 	              
         )); 

		
		if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Branch'),
                'index'     => 'store_id',
            	'filter_index' => 'main_table.store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }
		
		$this->addColumn('name', array(
            'header' => Mage::helper('sales')->__('Product Name'),
            'index' => 'name',
            'width' => '100px',
        ));
          	
		
		$this->addColumn('sku', array(
            'header' => Mage::helper('sales')->__('Product Sku'),
            'index' => 'sku',
            'width' => '100px',
			
        ));
		
		
		$this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Ordered On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
			'filter_index' => 'main_table.created_at',
        )); 
		
		
		$this->addColumn('shipping_delivery_date', array(
            'header' => Mage::helper('sales')->__('Delivery Date'),
            'index' => 'shipping_delivery_date',
            'type' => 'datetime',
            'width' => '100px',
			'filter_index' => 'sales_flat_order.shipping_delivery_date',
        )); 
	 
		
		$this->addColumn('qty_ordered', array(
            'header' => Mage::helper('sales')->__('Qty'),
            'index' => 'qty_ordered',
            'width' => '100px',
        ));
		
		$this->addColumn('base_price_incl_tax', array(
            'header' => Mage::helper('sales')->__('Price'),
            'index' => 'base_price_incl_tax',
            'width' => '100px',
        ));
		
		$this->addColumn('base_row_total_incl_tax', array(
            'header' => Mage::helper('sales')->__('Total'),
            'index' => 'base_row_total_incl_tax',
            'width' => '100px',
        ));
      /*Edit by 24122017 Islam ELgarhy*/
        $this->addColumn('discount_cause', array(
            'header' => Mage::helper('sales')->__('Discount Cause'),
            'index' => 'discount_cause',
            'width' => '100px',
        ));

        $this->addColumn('discount_by', array(
            'header' => Mage::helper('sales')->__('Discount Approved_By'),
            'index' => 'discount_by',
            'width' => '100px',
        ));

        
		       /*Edit by 24122017 Islam ELgarhy*/
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('sales')->__('XML'));
        
        return parent::_prepareColumns();
    }


//	public function getGridUrl()
 //   {
      //  return $this->getUrl('*/*/grid', array('_current'=>true));
//    }

	  
}

