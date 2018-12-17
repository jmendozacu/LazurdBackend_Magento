<?php

//ini_set('max_execution_time', 120);
//ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '')
{
    	try
		{
		
			$driverId =  $_REQUEST['driver_id'];
			$resource = Mage::getSingleton('core/resource');
			$connectionRead = $resource->getConnection('core_read');
			$driverdata = Mage::getModel('dirvermanagement/driver')->getCollection()
				->addFieldToFilter('userid', array('eq' => $driverId));
			if($driverdata->getData() != array())
			{
				setHeader(200);                
				$response['code'] = 200;
				$response['status'] = $responsecodes[$response['code']];
			
				 //if ($_REQUEST['order_type'] == 1){
				
					$order_status = '"delivered"';
					$select ='SELECT count(sales_flat_order.entity_id) FROM sales_flat_order
					WHERE  sales_flat_order.driver_id = '.$driverId.' AND  sales_flat_order.order_status = '.$order_status.' AND sales_flat_order.increment_id  IN
					(SELECT driver_delivery_information.order_id FROM driver_delivery_information
					WHERE  driver_delivery_information.driver_id = '.$driverId.' AND DATE(driver_delivery_information.created_date) = CURDATE())';
					$response['ordersCompleted']= $connectionRead->fetchrow($select)['count(sales_flat_order.entity_id)'];
				//}
				
				//else if ($_REQUEST['order_type'] == 2){

					$order_status = '"returned"';
					$select ='SELECT count(sales_flat_order.entity_id) FROM sales_flat_order
					WHERE  sales_flat_order.driver_id = '.$driverId.' AND  sales_flat_order.order_status = '.$order_status.' AND sales_flat_order.increment_id  IN
					(SELECT driver_delivery_information.order_id FROM driver_delivery_information
					WHERE  driver_delivery_information.driver_id = '.$driverId.' AND driver_delivery_information.reason IS NOT NULL AND DATE(driver_delivery_information.created_date) = CURDATE())';
					$response['ordersCanceled']= $connectionRead->fetchrow($select)['count(sales_flat_order.entity_id)'];
				//}

				//else if ($_REQUEST['order_type'] == 3){

					$order_status = '"dispatched"';
					$select ='SELECT count(sales_flat_order.entity_id) FROM sales_flat_order
					WHERE  sales_flat_order.driver_id = '.$driverId.' AND  sales_flat_order.order_status = '.$order_status.' AND sales_flat_order.increment_id NOT IN
					(SELECT driver_delivery_information.order_id FROM driver_delivery_information
					WHERE  driver_delivery_information.driver_id = '.$driverId.')';

					$response['ordersDispatched']= $connectionRead->fetchrow($select)['count(sales_flat_order.entity_id)'];
				//}
			

				//

				$select= 'SELECT SUM(payment_amount) FROM webpos_order_payment WHERE webpos_order_payment.order_id IN( SELECT entity_id FROM sales_flat_order WHERE increment_id IN (SELECT order_id FROM driver_delivery_information WHERE driver_id ='.$driverId.' AND reason IS NULL AND DATE(driver_delivery_information.created_date) = CURDATE())) AND method = "codforpos"';
				$tcash = $connectionRead->fetchrow($select)['SUM(payment_amount)'];
				$response['TotalCash']= ($tcash == null ? "0.00" : number_format($tcash, 2, '.', ''));
				
				//
				echo json_encode($response);
				die;
			}
			else
			{
				setHeader(603);
				$message = 'Driver not found.';
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
