<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '')
{
    
	
	
		try
		{
			$orderId =  $_REQUEST['increment_id'];
			
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
			$select = $connectionRead->select()->from('driver_time', array('*'))->where('order_number=?',$orderId);
			$driverCollection =$connectionRead->fetchRow($select);
			
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
			$select = $connectionRead->select()->from('sales_order_status_label', array('*'))
											   ->where('status=?',$driverCollection['status'])
											   ->where('store_id=?',$_REQUEST['lang']);
			$labelCollection =$connectionRead->fetchRow($select);
			
			
			$driver_id = $_REQUEST['driver_id'];
			
			if($driver_id)
			{
				$select1 = $connectionRead->select()->from('deliveryboy_location', array('*'))->where('deliveryboy_id=?',$driver_id);
				$locationCollection =$connectionRead->fetchRow($select1);
				
				$latitude = $locationCollection['latitude'];
				$longitude =  $locationCollection['longitude'];
				
				$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
				$shippingAddress = $order->getShippingAddress();
				
				$addressValue = Mage::getModel('customer/address')->load($shippingAddress->getData('customer_address_id'));
				
				setHeader(200);
				$response['code'] = 200;
				$response['status'] = $responsecodes[$response['code']];
				
				$response['driver_id'] = ($driver_id) ? $driver_id : '';
				if($labelCollection['label'])
					$response['order_status'] = ($labelCollection['label']) ? $labelCollection['label'] : '';
			    else
					$response['order_status'] = '';
				
				$response['latitude'] = ($latitude) ? $latitude : '';
				$response['longitude'] = ($longitude) ? $longitude : '';
				$response['user']['latitude'] = ($addressValue->getData('customer_lat')) ? $addressValue->getData('customer_lat') : "";
				$response['user']['longitude'] = ($addressValue->getData('customer_lon')) ? $addressValue->getData('customer_lon') : "";
				
				echo json_encode($response);
				die;	
			}
			else
			{
				setHeader(603);
				$message ='Driver not assign for this order.' ;
				echo json_encode(array('code'=>603,'status'=>'error','message'=>$message));
				die;
			}
		}
		catch(Exception $e)
		{
			$e = json_decode(json_encode($e), true);
			include('exception_handler.php');
		}
	
}
else
{
    setHeader(400);
    echo json_encode(array('code'=>400,'status'=>'error','message'=>$responsecodes['400']));
    die;
}
?>
