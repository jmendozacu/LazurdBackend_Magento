<?php
class Ewall_Dirvermanagement_Adminhtml_DriverlocationController extends Mage_Adminhtml_Controller_Action
{


	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Location"));
	   $this->renderLayout();
    }

    public function markerdisplayAction(){

     // $orderCollection = Mage::getResourceModel('sales/order_collection');
      
//	  $orderCollection->addFieldToFilter('order_status', array(
//            array('name'=>'order_status','neq'=>'canceled'),
//            //array('name'=>'order_status','neq'=>'delivered'), 
//            //array('name'=>'order_status','neq'=>'returned')            
//        ))
//      ->addFieldToFilter('driver_id', array('neq' => null));
//      $driver_ids = array_column($orderCollection->getData(), 'driver_id');
	  	
		
      $to_time = Mage::getModel('core/date')->date('Y-m-d H:i:s');
      $from_time = strtotime($to_time) - 60;
      $driverdata = Mage::getModel('dirvermanagement/driver')->getCollection();
	                 //->addFieldToFilter('userid', array('in' => $driver_ids));
					 //->addFieldToFilter('updated_at', array('from' =>  date("Y-m-d H:i:s", $from_time), 'to' => $to_time));
	  //echo "<pre>";print_r($driverdata->getData());die;
      $locations = array();
      foreach ($driverdata as $key => $location) {
	        $adminuserdata = Mage::getModel('admin/user')->load($location->getUserid());
          	$locations[$key]['name'] = $adminuserdata["firstname"].' '.$adminuserdata["lastname"];
          	$locations[$key]['latitude'] =  $location->getLatitude();
          	$locations[$key]['longitude'] = $location->getLongitude();
      }
      echo json_encode($locations);
      exit;
    }
}