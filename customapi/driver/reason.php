<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');
try
{
	$resource = Mage::getSingleton('core/resource');
	$connectionRead = $resource->getConnection('core_read');
	$query = 'SELECT core_order_return_reason.* FROM  core_order_return_reason';
	$results = $connectionRead->fetchAll($query);				
	$collection = Mage::getResourceModel('core/order_return_reason');
	setHeader(200);
	$response['code'] = 200;
	$response['status'] = $responsecodes[$response['code']];
	$response['reason'] = $results;
	echo json_encode($response);
	die;	
	/*
	$reason = array();
	$reason['Test-1'] = 'Test-1';
	$reason['Test-2'] = 'Test-2';
	$reason['Test-3'] = 'Test-3';
	$reason['Test-4'] = 'Test-4';
	$reason['Test-5'] = 'Test-5';
	
	//echo "<pre>";print_r($reason);die;
	
	setHeader(200);
	$response['code'] = 200;
	$response['status'] = $responsecodes[$response['code']];
	$response['reason'] = $reason;
	

	echo json_encode($response);
	die;		

	*/
	
}
catch(Exception $e)
{
	$e = json_decode(json_encode($e), true);
	include('exception_handler.php');
}
	

?>
