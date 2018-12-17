<?php


class Ewall_Dirvermanagement_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct()
	{

		$this->_controller = "adminhtml_report";
		$this->_blockGroup = "dirvermanagement";
		$this->_headerText = Mage::helper("dirvermanagement")->__("Order Delivery Report");
		parent::__construct();
		$this->_removeButton('add');

	}

	public function getDriverOrderCollection(){
		$drivers = Mage::getModel('admin/user')->getCollection();
        $drivers->getSelect()->joinLeft(array('o'=> 'admin_role'), "o.user_id = main_table.user_id" ,array('*'));
        $drivers->addFieldToFilter('parent_id' , 5);
        return $drivers;

	}
	public function getdriverOrders($id){
		$data = $this->getRequest()->getPost();
		$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('driver_id' , $id);
		$orders->addFieldToFilter('created_at', array('from'=> $data['form-date'],'to'=> $data['to-date'].' 23:59:59','date' => true));		
		//$orders->addFieldToFilter('order_status' , array('eq' => 'pending'));
		$orders->addFieldToFilter('order_status' , array(array('eq' => 'delivered'), array('eq' => 'returned')));
		
        if($data['delivery']){
        	$orders->addFieldToFilter('order_status' , ['neq' => 'delivered']);
        }
        return $orders;
	}

}