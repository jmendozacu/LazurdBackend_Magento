<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '' &&
   isset($_REQUEST['latitude']) && $_REQUEST['latitude'] != '' &&
   isset($_REQUEST['longitude']) && $_REQUEST['longitude'] != '')
{
		try
		{
			
			//$select = $connectionRead->select()->from('driver_time', array('*'))->where('order_number=?',$orderId);
			//$driverCollection =$connectionRead->fetchRow($select);
			$driver_id = $_REQUEST['driver_id'];
			$latitude = $_REQUEST['latitude'];
			$longitude =  $_REQUEST['longitude'];
			$mobile = ($_REQUEST['mobile']) ? $_REQUEST['mobile']  : '';
			$getmobile = $_REQUEST['get_mobile'];
			
			$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
			$select1 = $connectionRead->select()->from('driver_management', array('*'))->where('userid=?',$driver_id);
			$locationCollection =$connectionRead->fetchRow($select1);
			
			//echo "<pre>";print_r($locationCollection);die;
			
			$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
			
			if($locationCollection != '')
			{
				if($getmobile == true)
				{
					$selectQue = $connectionRead->select()->from('driver_management', 'mobile')->where('userid=?',$driver_id);
					$results = $connectionRead->fetchRow($selectQue);		
					setHeader(200);
					$response['code'] = 200;
					$response['status'] = $responsecodes[$response['code']];
					$response['driver_id'] = $driver_id;
					$response['mobile'] = $results;
					$response['success_message'] = "Data Successfully retrive.";
					echo json_encode($response);
					die;	
				}
				$updateQue = 'UPDATE `driver_management` SET mobile="'.$mobile.'",latitude="'.$latitude.'", longitude="'.$longitude.'", updated_at="'.date('Y-m-d H:i:s').'" WHERE userid='.$driver_id;
				
				//echo $updateQue;die;
				
				if($db_write1->query($updateQue)){
					
					setHeader(200);
					$response['code'] = 200;
					$response['status'] = $responsecodes[$response['code']];
					$response['driver_id'] = $driver_id;
					$response['success_message'] = "Data Successfully updated.";
				}else{
					setHeader(200);
					$response['code'] = 200;
					$message = 'Error in update.' ;
				}
			}
			else{
				

				if($getmobile == true)
				{
					$selectQue = $connectionRead->select()->from('driver_management', 'mobile')->where('userid=?',$driver_id);
					$results = $connectionRead->fetchRow($selectQue);		
					setHeader(200);
					$response['code'] = 200;
					$response['status'] = $responsecodes[$response['code']];
					$response['driver_id'] = 'New User';
					$response['mobile'] = 'New User';
					$response['success_message'] = "Data Successfully retrive.";
					echo json_encode($response);
					die;	
				}

				$sql = 'INSERT INTO `driver_management` (`userid`,`mobile`, `latitude`, `longitude`, `updated_at`) VALUES ("'.$driver_id.'","'.$mobile.'","'.$latitude.'","'.$longitude.'","'.date('Y-m-d H:i:s').'")';
				
				if($db_write1->query($sql)){
					setHeader(200);
					$response['code'] = 200;
					$response['status'] = $responsecodes[$response['code']];
					$response['driver_id'] = $driver_id;
					$response['success_message'] = "Data Successfully added in record.";
				}
			}
			
			echo json_encode($response);
			die;	
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
