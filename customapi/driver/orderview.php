<?php

ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['increment_id']) && $_REQUEST['increment_id'] != '')
{
    
		$connectionURL = $baseurl.'/index.php/'.$protocol[$protocolKey];
		
		try
		{
			$client = new SoapClient($connectionURL);
			$session = $client->login($api_username, $api_key);
			//echo $session;die;
			if($session)
			{
				$increment_id =  $_REQUEST['increment_id'];
							
				$result = $client->salesOrderInfo($session, $increment_id);
				$result = (array)$result;
				
				//echo "<pre>";print_r($result);die;
				if($result)
				{
					setHeader(200);                
					$response['code'] = 200;
					$response['status'] = $responsecodes[$response['code']];
					$response['order']['increment_id'] = ($increment_id) ? $increment_id : '';
					
					$orderData= Mage::getModel('sales/order')->loadByIncrementId($increment_id);
					
					$date = date_create($result['created_at']);
					
					//$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
					//$select = $connectionRead->select()->from('sales_order_status_label', array('*'))
					//								   ->where('status=?',$orderData->getStatus())
					//								   ->where('store_id=?',$_REQUEST['lang']);
					//$labelCollection =$connectionRead->fetchRow($select);
					
					$response['order']['staff_id'] = $result['webpos_staff_id'];
					$response['order']['staff_name'] = $result['webpos_staff_name'];
					
					$response['order']['status'] = ($orderData->getStatus()) ? $orderData->getStatus() : '';
					$response['order']['created_at'] = ($result['created_at']) ? date_format($date,'d/m/Y') : '';
					$response['order']['subtotal'] = ($result['subtotal']) ? number_format($result['subtotal'], 2, '.', '') : '';
					$response['order']['delivery_fee'] = ($orderData->getShippingAmount()) ? number_format($orderData->getShippingAmount(),2, '.', '') : " ";
					$response['order']['grand_total'] = ($result['grand_total']) ? number_format($result['grand_total'], 2, '.', '') : '';
					
					if($orderData->getAppliedRuleIds() >= 1)
					{
						$ruleId = $orderData->getAppliedRuleIds();
						$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
						$selectRowLabel = $connectionRead->select()->from('salesrule_label', array('*'))->where('rule_id=?',$ruleId);
						$labelValue =$connectionRead->fetchRow($selectRowLabel);
						
						if(strpos($total['title'], 'Discount') !== false){
							$response['order']['discount_label'] = $labelValue['label'];
							$response['order']['discount_amount'] = number_format($total['amount'], 2, '.', '');
						}	
					}else{
						$response['order']['discount_label'] = "";
						$response['order']['discount_amount'] = number_format(0.00, 2, '.', '');
					}
					
					//$shippingAddress = (array)$result['shipping_address'];
					
					//
					$shippingAddress = Mage::getModel('customer/customer')->load($result['customer_id'])->getPrimaryShippingAddress();
					//$userAddress = array();
					//if($shippingAddress)
					//{
					//	$userAddress[] = ($shippingAddress['street']) ? $shippingAddress['street'] : "";
					//	$userAddress[] = ($shippingAddress['city']) ? $shippingAddress['city'] : "";
					//	$userAddress[] = ($shippingAddress['region']) ? $shippingAddress['region'] : "";
					//	$country = Mage::getModel('directory/country')->loadByCode($shippingAddress['country_id']);
					//	$userAddress[] = ($country->getName()) ? $country->getName() : "";
					//}
					//$imploDaadres = implode(' ',$userAddress);
					////$postAddress = str_replace(' ','+',$imploDaadres);
					//
					//$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.urlencode($imploDaadres).'&sensor=false');
					//$output= json_decode($geocode);
					//echo "<pre>";print_r($output);die;
					//echo $latitude = $output->results[0]->geometry->location->lat;
					//echo '<br>';
					//echo $longitude = $output->results[0]->geometry->location->lng;
					//die;
	
					if($shippingAddress)
					{
						
						if($shippingAddress['firstname'] && $shippingAddress['lastname']){
						$response['order']['Shipping_Address']['name'] = $shippingAddress['firstname'] .' '. $shippingAddress['lastname'];
						}elseif($shippingAddress['firstname']){
							$response['order']['Shipping_Address']['name'] = ($shippingAddress['firstname']) ? $shippingAddress['firstname'] : '';
						}elseif($shippingAddress['lastname']){
							$response['order']['Shipping_Address']['name'] = ($shippingAddress['lastname']) ? $shippingAddress['lastname'] : '';
						}else
							 $response['order']['Shipping_Address']['name'] = '';
							 
						$response['order']['Shipping_Address']['block'] = ($shippingAddress['company']) ? $shippingAddress['company'] : "";
						$response['order']['Shipping_Address']['region_ar'] = ($shippingAddress['region_ar']) ? $shippingAddress['region_ar'] : "";
						$response['order']['Shipping_Address']['city'] = ($shippingAddress['city']) ? $shippingAddress['city'] : "";
						$response['order']['Shipping_Address']['region'] = ($shippingAddress['region']) ? $shippingAddress['region'] : "";
						$country = Mage::getModel('directory/country')->loadByCode($shippingAddress['country_id']);
						$response['order']['Shipping_Address']['country'] = ($country->getName()) ? $country->getName() : "";
						$response['order']['Shipping_Address']['street'] = ($shippingAddress['street']) ? $shippingAddress['street'] : "";
						$response['order']['Shipping_Address']['telephone'] = ($shippingAddress['telephone']) ? $shippingAddress['telephone'] : "";
						
						$response['order']['Shipping_Address']['customer_lat'] = ($shippingAddress['customer_lat']) ? $shippingAddress['customer_lat'] : "";
						$response['order']['Shipping_Address']['customer_lon'] = ($shippingAddress['customer_lon']) ? $shippingAddress['customer_lon'] : "";
					}
					else
					{
						$response['order']['Shipping_Address'] = array();
					}
					
					$response['order']['Delivery Method']['shipping_description'] = ($result['shipping_description']) ? $result['shipping_description'] : '';
					
					$paymentMethod = (array)$result['payment'];
					if($paymentMethod)
					{
						//$name = Mage::helper('payment')->getMethodInstance($paymentMethod['method'])->getTitle();
						//$response['order']['Payment Method']['name'] = ($name) ? $name : " "; 
						
						
						$resource = Mage::getSingleton('core/resource');
						$connectionRead = $resource->getConnection('core_read');
						$query = 'SELECT *
						FROM  webpos_order_payment 
						WHERE webpos_order_payment.order_id  in ( SELECT entity_id from sales_flat_order Where increment_id= '.$increment_id .')';
						Mage::log($query ,null,'mylog55.log');
						$paymentstresults = $connectionRead->fetchAll($query);
						$response['order']['Payment_Method']= $paymentstresults;

					}
					else
					{
						$response['order']['Payment_Method'] = array();
					}
					
					$i=0;
					foreach($result['items'] as $key)
					{
						$key = (array)$key;
						$response['order']['product'][$i]['product_id'] = ($key['product_id']) ? $key['product_id'] : " ";
						$response['order']['product'][$i]['product_name'] = ($key['name']) ? $key['name'] : " ";
						$response['order']['product'][$i]['product_sku'] = ($key['sku']) ? $key['sku'] : " ";
						$response['order']['product'][$i]['base_price'] = ($key['base_price']) ? number_format($key['base_price'], 2, '.', '') : " ";
						$response['order']['product'][$i]['product_qty'] = ($key['qty_ordered']) ? number_format($key['qty_ordered']) : " ";
						$response['order']['product'][$i]['row_total'] = ($key['row_total']) ? number_format($key['row_total'], 2, '.', '') : " ";
						
						$i++;
					}
				    echo json_encode($response);
				    die;
				}
				else
				{
					setHeader(603);
					$message = 'Orders not found.';
					echo json_encode(array('code'=>603,'status'=>'error','message'=>$message));
					die;
				}
				
			}
			else
			{
				setHeader(602);
				$message ='Session has been expired. Please login again.';
				echo json_encode(array('code'=>602,'status'=>'error','message'=>$message));
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
