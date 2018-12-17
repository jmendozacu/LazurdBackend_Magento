<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '' &&
   isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '' &&
   isset($_REQUEST['reason']) && $_REQUEST['reason'] != '' &&
   isset($_REQUEST['note']) && $_REQUEST['note'] != '')
{
		try
		{
			$driver_id = $_REQUEST['driver_id'];
			$order_id = $_REQUEST['order_id'];
			$reason =  $_REQUEST['reason'];
			$note =  $_REQUEST['note'];
			
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
			$select1 = $connectionRead->select()
								->from('driver_delivery_information', array('*'))
								->where('driver_id=?',$driver_id)
								->where('order_id=?',$order_id);
			
			$deliveryCollection = $connectionRead->fetchRow($select1);
			//echo "<pre>";print_r($deliveryCollection);die;
			$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
			if($deliveryCollection == '')
			{				
				$sql = 'INSERT INTO `driver_delivery_information` (`driver_id`, `order_id`, `reason`, `note`, `created_date`) VALUES ("'.$driver_id.'","'.$order_id.'","'.$reason.'","'.$note.'","'.date('Y-m-d H:i:s').'")';
				//echo $sql;die;

				$sql_2 = 'UPDATE `sales_flat_order` SET `state` = "processing", `order_status` = "returned" , `status` = "correction" WHERE `sales_flat_order`.`increment_id` =' .$order_id;
				$sql_3 = 'UPDATE `sales_flat_order_grid` SET `order_status` = "returned" , `status` = "correction"  WHERE `sales_flat_order_grid`.`increment_id` =' .$order_id;

				/*
				$selectQue = $connectionRead->select()->from('sales_flat_order', 'entity_id')->where('increment_id =?',$order_id);
				$parent_id = $connectionRead->fetchRow($selectQue)['entity_id'];		
				$comment = 'Order Canceled From Mobile Delivery Application';
				$status1='pending';
				$custom_order_status1='returned';
				$status2='correction';
				$custom_order_status2='returned';
				$sql_4 = 'INSERT INTO `sales_flat_order_status_history` (`parent_id`,`comment`, `created_at`,`status`,`custom_order_status`) VALUES ("'.$parent_id.'","'.$comment.'","'.date('Y-m-d H:i:s').'","'.$status1.'","'.$custom_order_status1.'"),
				("'.$parent_id.'","'.$comment.'","'.date('Y-m-d H:i:s').'","'.$status2.'","'.$custom_order_status2.'")';
				*/

				$selectQue = $connectionRead->select()->from('sales_flat_order', 'entity_id')->where('increment_id =?',$order_id);
				$parent_id = $connectionRead->fetchRow($selectQue)['entity_id'];		
				$comment = 'Order Canceled From Mobile Delivery Application';
				$selectQue2 = $connectionRead->select()->from('sales_flat_order', 'status')->where('increment_id =?',$order_id);
				$status1 = $connectionRead->fetchRow($selectQue)['status'];	
				$custom_order_status1='returned';	
			
				$sql_4 = 'INSERT INTO `sales_flat_order_status_history` (`parent_id`,`comment`, `created_at`,`status`,`custom_order_status`) VALUES ("'.$parent_id.'","'.$comment.'","'.date('Y-m-d H:i:s').'","'.$status1.'","'.$custom_order_status1.'")';
			

				if($db_write1->query($sql))
				{
					if(!($db_write1->query($sql_2) && $db_write1->query($sql_3) && $db_write1->query($sql_4)))
					{		
						setHeader(200);
						$response['code'] = 200;
						$response['success_message'] = "Error in update Status." ;				
					}
					else
					{
						setHeader(200);
						$response['code'] = 200;
						$response['status'] = $responsecodes[$response['code']];
						$response['driver_id'] = $driver_id;
						$response['success_message'] = "Record Successfully added.";
					}			

				}else{
					setHeader(200);
					$response['code'] = 200;
					$response['success_message'] =  "Error in added.";
				}
				echo json_encode($response);
				die;		
				
			}else{
				setHeader(200);
				$response['code'] = 200;
				$response['success_message'] = "Order Allredy shipped to the customer." ;
				echo json_encode($response);
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
