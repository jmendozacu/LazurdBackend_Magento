<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

//$data = json_decode(file_get_contents('php://input'), true);
//echo "<pre>";print_r($data);

//$driver_id = $_POST['driver_id'];
//$order_id = $_POST['order_id'];
//$received_name =  $_POST['received_name'];
//$sign =  $_POST['sign'];

//$preFileName = '';
//if($sign != ''){
//	/*Blog data covert to Image and save name in database*/
//	$data = base64_decode($sign);
//	$preFileName = "D".$driver_id."_O".$order_id."_".rand().'.png';
//	$file = 'sign/'.$preFileName;
//	$success = file_put_contents($file, $data);
//	$data = base64_decode($data); 
//	$source_img = imagecreatefromstring($data);
//	$rotated_img = imagerotate($source_img, 90, 0); 
//	$file = 'sign/'. $preFileName;
//	$imageSave = imagejpeg($rotated_img, $file, 10);
//	imagedestroy($source_img);
//	
//	setHeader(200);
//	$response['code'] = 200;
//	$response['sign'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'customapi/driver/sign/'.$preFileName;
//	$response['success_message'] = "Image generated.";
//	
//}
//else{
//	setHeader(200);
//	$response['code'] = 200;
//	$response['success_message'] = "no generated.";
//}
//
//echo json_encode($response);
//die;

$_POST = json_decode(file_get_contents('php://input'), true);
/*
if((isset($_POST['driver_id']) && $_POST['driver_id'] != '' &&
isset($_POST['order_id']) && $_POST['order_id'] != '' &&
isset($_POST['received_name']) && $_POST['received_name'] != '' &&
isset($_POST['sign']) && $_POST['sign'] != ''))
*/
if(true)
{
	try{
//		echo "<pre>";print_r($_POST);die;
		$driver_id = $_POST['driver_id'];
		$order_id = $_POST['order_id'];
		$received_name =  $_POST['received_name'];
		$sign =  $_POST['sign'];
		// added by AVE
        $cash_received = 0;
        if (isset($_POST['cash_collected']))
		{
            $cash_received=$_POST['cash_collected'];
		}
	
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
			$preFileName = '';
			if($sign != ''){
				/*Blog data covert to Image and save name in database*/
				//$data = base64_decode($sign);
				//$preFileName = "D".$driver_id."_O".$order_id."_".rand().'.png';
				//$file = 'sign/'.$preFileName;
				//$success = file_put_contents($file, $data);
				//$data = base64_decode($data); 
				//$source_img = imagecreatefromstring($data);
				//$rotated_img = imagerotate($source_img, 90, 0); 
				//$file = 'sign/'. $preFileName;
				//$imageSave = imagejpeg($rotated_img, $file, 10);
				//imagedestroy($source_img);
				
				

				$sign = str_replace('data:image/png;base64,', '', $sign);
				$sign = str_replace(' ', '+', $sign);
				$sign = base64_decode($sign);
				$preFileName = "D".$driver_id."_O".$order_id."_".rand().'.png';
				$file = 'sign/'.$preFileName;
				$success = file_put_contents($file, $sign);

				/*$sign = base64_decode($sign); 
				$source_img = imagecreatefromstring($sign);
				$rotated_img = imagerotate($source_img, 90, 0); 
				$preFileName = "D".$driver_id."_O".$order_id."_".rand().'.png';
				$file = 'sign/'.$preFileName;
				$imageSave = imagejpeg($rotated_img, $file, 10);
				imagedestroy($source_img);*/
			}
			
			$sql = 'INSERT INTO `driver_delivery_information` (`driver_id`, `order_id`, `received_by`, `sign`, `created_date`,`cash_received`) VALUES ("'.$driver_id.'","'.$order_id.'","'.$received_name.'","'.$preFileName.'","'.date('Y-m-d H:i:s').'",'.$cash_received.')';
			//echo $sql;die;

			$sql_2 = 'UPDATE  `sales_flat_order` SET `state` = "complete", `status` = "complete" , `order_status` = "delivered" WHERE `sales_flat_order`.`increment_id` ='.$order_id;
			$sql_3 = 'UPDATE `sales_flat_order_grid` SET `status` = "complete" , `order_status` = "delivered" WHERE `sales_flat_order_grid`.`increment_id` =' .$order_id;
			
		
			$selectQue = $connectionRead->select()->from('sales_flat_order', 'entity_id')->where('increment_id =?',$order_id);
			$parent_id = $connectionRead->fetchRow($selectQue)['entity_id'];	
			$status1='pending';
			$custom_order_status1='delivered';
			$status2='complete';
			$custom_order_status2='delivered';
			$selectQue2 = $connectionRead->select()->from('driver_management', 'mobile')->where('userid =?',$driver_id);
			$mobile = $connectionRead->fetchRow($selectQue2)['mobile'];	
			$comment1 = 'Order Drop off From Mobile Delivery Application with Driver Mobile(' . $mobile . ') with Order Status(' . $status1. ')';
			$comment2 = 'Order Drop off From Mobile Delivery Application with Driver Mobile(' . $mobile . ') with Order Status(' . $status2. ')';
			$sql_4 = 'INSERT INTO `sales_flat_order_status_history` (`parent_id`,`comment`, `created_at`,`status`,`custom_order_status`) VALUES ("'.$parent_id.'","'.$comment1.'","'.date('Y-m-d H:i:s').'","'.$status1.'","'.$custom_order_status1.'"),
			("'.$parent_id.'","'.$comment2.'","'.date('Y-m-d H:i:s').'","'.$status2.'","'.$custom_order_status2.'")';


			if($db_write1->query($sql))
			{
				if(!($db_write1->query($sql_2) && $db_write1->query($sql_3)&& $db_write1->query($sql_4)))
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
					$response['sign'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'customapi/driver/sign'.$preFileName;
					$response['success_message'] = "Record Successfully added.";

				}
			}
			else
			{
				setHeader(200);
				$response['code'] = 200;
				$response['success_message'] = "Error in added." ;
			}
			echo json_encode($response);
			die;		
			
		}
		else
		{
			setHeader(200);
			$response['code'] = 200;
			$response['success_message'] = "Order Allredy shipped to the customer.";
			$response['deliveryCollection'] = $deliveryCollection;
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
