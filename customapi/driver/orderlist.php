<?php

//ini_set('max_execution_time', 120);
//ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '' &&
   isset($_REQUEST['order_status']) && $_REQUEST['order_status'] != '')
{
    	try
		{
			$page = 1;
			$limit = 10;
			if(isset($_REQUEST['page']) && $_REQUEST['page'] != null && $_REQUEST['page']>=1)
			{
				$page = $_REQUEST['page'];
			}
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
				
				if($_REQUEST['order_status'] == 2)
				{
					$offset = ($page - 1) * $limit;
					
					$query = 'SELECT driver_delivery_information.*,sales_flat_order.* 
					FROM  driver_delivery_information RIGHT JOIN '.Mage::getSingleton('core/resource')->getTableName('sales/order').' 
					ON sales_flat_order.increment_id=driver_delivery_information.order_id 
					WHERE sales_flat_order.order_status = "dispatched" AND driver_delivery_information.driver_id='.$driverId.' LIMIT '.$offset.','.$limit;
					$results = $connectionRead->fetchAll($query);
					
					$response['Count'] =  count($results);
					$j = 0;
					foreach($results as $key)
					{
						if($key['increment_id'])
							$response['order'][$j]['increment_id'] = ($key['increment_id']) ?$key['increment_id'] : '';
						else
							$response['order'][$j]['increment_id'] = '';
								       
						if($key['status'])
							$response['order'][$j]['status'] = ($key['status']) ? $key['status'] : '';
						else
							$response['order'][$j]['status'] = '';
							
						if($key['created_at']){
							$date=date_create($key['created_at']);
							$response['order'][$j]['created_at'] = ($key['created_at']) ? date_format($date,'d/m/Y') : '';
						}
						else
							$response['order'][$j]['created_at'] = '';

						if($key['shipping_delivery_date']){
								$date=date_create($key['shipping_delivery_date']);
								$response['order'][$i]['shipping_delivery_date'] = ($key['shipping_delivery_date']) ? date_format($date,'d/m/Y h:i:s') : '';
							}
						else
							$response['order'][$i]['shipping_delivery_date'] = '';
					       
						if($key['customer_firstname'] && $key['customer_lastname']){
						$response['order'][$j]['ship_to'] = $key['customer_firstname'] .' '. $key['customer_lastname'];
						}elseif($key['firstname']){
						       $response['order'][$j]['ship_to'] = ($key['customer_firstname']) ? $key['customer_firstname'] : '';
						}elseif($key['lastname']){
						       $response['order'][$j]['ship_to'] = ($key['customer_lastname']) ? $key['customer_lastname'] : '';
						}else
							$response['order'][$j]['ship_to'] = '';
					       
						if($key['grand_total'])
							$response['order'][$j]['grand_total'] = ($key['grand_total']) ? number_format($key['grand_total'], 2, '.', '')  : '';
						else
							$response['order'][$j]['grand_total'] = '';
							
						$response['order'][$j]['driver_order_status'] = ($key['received_by'] != '') ? 	'delivered' : 'return';
						
						$j++;
					}
					
				}else{
					$offset = ($page - 1) * $limit;
					
					$select ='SELECT sales_flat_order.* FROM sales_flat_order
					WHERE  sales_flat_order.order_status = "dispatched" AND sales_flat_order.driver_id = '.$driverId.' AND sales_flat_order.increment_id NOT IN
					(SELECT driver_delivery_information.order_id FROM driver_delivery_information
					WHERE   driver_delivery_information.driver_id = '.$driverId.') order by sales_flat_order.shipping_delivery_date ASC LIMIT '.$offset.','.$limit;
					
					


					$orders = $connectionRead->fetchAll($select);
						
					$count = count($orders);
					$response['Count'] = ($count) ? $count : '0';
					$i=0;
					foreach($orders as $key)
					{
						if($key['increment_id'])
							$response['order'][$i]['increment_id'] = ($key['increment_id']) ?$key['increment_id'] : '';
						else
							$response['order'][$i]['increment_id'] = '';
								       
						if($key['status'])
							$response['order'][$i]['status'] = ($key['status']) ? $key['status'] : '';
						else
							$response['order'][$i]['status'] = '';
							
						if($key['created_at']){
							$date=date_create($key['created_at']);
							$response['order'][$i]['created_at'] = ($key['created_at']) ? date_format($date,'d/m/Y') : '';
						}
						else
							$response['order'][$i]['created_at'] = '';
						
						if($key['shipping_delivery_date']){
								$date=date_create($key['shipping_delivery_date']);
								$response['order'][$i]['shipping_delivery_date'] = ($key['shipping_delivery_date']) ? date_format($date,'d/m/Y H:i:s') : '';
							}
						else
							$response['order'][$i]['shipping_delivery_date'] = '';
								
						if($key['customer_firstname'] && $key['customer_lastname']){
						$response['order'][$i]['ship_to'] = $key['customer_firstname'] .' '. $key['customer_lastname'];
						}elseif($key['firstname']){
							$response['order'][$i]['ship_to'] = ($key['customer_firstname']) ? $key['customer_firstname'] : '';
						}elseif($key['lastname']){
							$response['order'][$i]['ship_to'] = ($key['customer_lastname']) ? $key['customer_lastname'] : '';
						}else
							 $response['order'][$i]['ship_to'] = '';
						
						if($key['grand_total'])
							$response['order'][$i]['grand_total'] = ($key['grand_total']) ? number_format($key['grand_total'], 2, '.', '')  : '';
						else
							$response['order'][$i]['grand_total'] = '';
						
						$response['order'][$i]['driver_order_status'] = 'Assign';
						/*Order History*/
						$i++;
					}
					//echo "<pre>";print_r($key->getData());die;
				}
				
				if(!isset($response['order']))
				{
					$response['order'] = array();
				}
				if($count > ($limit*$page))
				{
					$response['is_last'] = 0;
				}
				else
				{
					$response['is_last'] = 1;
				}
				//echo "<pre>";print_r($response);die;
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
