<?php

//ini_set('max_execution_time', 120);
//ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['driver_id']) && $_REQUEST['driver_id'] != '')
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
			
			$orders = Mage::getResourceModel('sales/order_collection')
					->addFieldToSelect('*')
					->addFieldToFilter('driver_id', $driverId)
					->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
					->setOrder('created_at', 'desc')
					->setCurPage($page) // 2nd page
					->setPageSize($limit);
					
			//echo "<pre>";print_r($orders->getData());die;
			
			$orderCollection = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('driver_id', $driverId)->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates())); 
			$count = count($orderCollection);
			
			if($orders)
			{
				setHeader(200);                
				$response['code'] = 200;
				$response['status'] = $responsecodes[$response['code']];
				$response['Count'] = ($count) ? $count : '0';
				
				$i=0;
				foreach($orders as $key)
				{
					//echo "<pre>";print_r($key->getData());die;
					
					if($key->getIncrementId())
						 $response['order'][$i]['increment_id'] = ($key->getIncrementId()) ? $key->getIncrementId() : '';
					else
						 $response['order'][$i]['increment_id'] = '';
								
									
					/*$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
					$select = $connectionRead->select()->from('sales_order_status_label', array('*'))
													   ->where('status=?',$key->getStatus());
													   //->where('store_id=?',$_REQUEST['lang']);
					$labelCollection =$connectionRead->fetchRow($select);*/
					
					//echo "<pre>";print_r($labelCollection);die;
					
					if($key->getStatus())
						 $response['order'][$i]['status'] = ($key->getStatus()) ? $key->getStatus() : '';
					else
						 $response['order'][$i]['status'] = '';
						 
					if($key->getCreatedAt()){
						 $date=date_create($key->getCreatedAt());
						 $response['order'][$i]['created_at'] = ($key->getCreatedAt()) ? date_format($date,'d/m/Y') : '';
					}
					else
						 $response['order'][$i]['created_at'] = '';
					
					if($key['customer_firstname'] && $key['customer_lastname']){
					$response['order'][$i]['ship_to'] = $key['customer_firstname'] .' '. $key['customer_lastname'];
					}elseif($key['firstname']){
						$response['order'][$i]['ship_to'] = ($key['customer_firstname']) ? $key['customer_firstname'] : '';
					}elseif($key['lastname']){
						$response['order'][$i]['ship_to'] = ($key['customer_lastname']) ? $key['customer_lastname'] : '';
					}else
						 $response['order'][$i]['ship_to'] = '';
					
					if($key->getGrandTotal())
						 $response['order'][$i]['grand_total'] = ($key->getGrandTotal()) ? number_format($key->getGrandTotal(), 2, '.', '')  : '';
					else
						 $response['order'][$i]['grand_total'] = '';
						 
						 
					/*if($key->getDriverId() != NULL && $key->getStatus() != 'delivered' && $key->getStatus() != 'return'){
						$response['order'][$i]['track_order'] = 1 ;
						$response['order'][$i]['track_order_link'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'index.php/trackdeliveryboy/index/index/?id='.$key->getIncrementId() ;
					}
					else
						$response['order'][$i]['track_order'] = 0;
						
					$col = Mage::getModel('reviews/reviewdata')->load($key->getIncrementId());
					if($key->getDriverId() != NULL && $key->getStatus() == 'delivered' && count($col->getData()) < 1){
						$response['order'][$i]['evaluate'] = 1;
						$response['order'][$i]['evaluate_link'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'index.php/reviews/menu/index/?id='.$key->getIncrementId();
					}
					else
						$response['order'][$i]['evaluate'] = 0;
					*/
					
					$i++;
					
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
