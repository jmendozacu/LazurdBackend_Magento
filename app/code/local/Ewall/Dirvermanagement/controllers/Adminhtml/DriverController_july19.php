<?php

class Ewall_Dirvermanagement_Adminhtml_DriverController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
	{
	//return Mage::getSingleton('admin/session')->isAllowed('dirvermanagement/driver');
		return true;
	}

	protected function _initAction()
	{
			$this->loadLayout()->_setActiveMenu("dirvermanagement/driver")->_addBreadcrumb(Mage::helper("adminhtml")->__("Driver  Manager"),Mage::helper("adminhtml")->__("Driver Manager"));
			return $this;
	}
	public function indexAction()
	{
		    $this->_title($this->__("Dirvermanagement"));
		    $this->_title($this->__("Manager Driver"));

			$this->_initAction();
			$this->renderLayout();
	}
	public function editAction()
	{
		    $this->_title($this->__("Dirvermanagement"));
			$this->_title($this->__("Driver"));
		    $this->_title($this->__("Edit Item"));

			$id = $this->getRequest()->getParam("user_id");
			// $model = Mage::getModel("dirvermanagement/driver")->getCollection();

			$collection = Mage::getModel('dirvermanagement/driver')->getCollection();
			$collection->getSelect()->joinLeft(array('oeu'=> 'admin_user'), "oeu.user_id = main_table.userid" ,array('*'));
			// $collection->getSelect()->joinLeft(array('o'=> 'admin_role'), "o.user_id = oeu.user_id" ,array('*'));
			$model = $collection->addFieldToFilter('userid',$id)->getFirstItem();


			if ($model->getId()) {
				Mage::register("driver_data", $model);
				$this->loadLayout();
				$this->_setActiveMenu("dirvermanagement/driver");
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Driver Manager"), Mage::helper("adminhtml")->__("Driver Manager"));
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Driver Description"), Mage::helper("adminhtml")->__("Driver Description"));
				$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
				$this->_addContent($this->getLayout()->createBlock("dirvermanagement/adminhtml_driver_edit"))->_addLeft($this->getLayout()->createBlock("dirvermanagement/adminhtml_driver_edit_tabs"));
				$this->renderLayout();
			}
			else {
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("dirvermanagement")->__("Item does not exist."));
				$this->_redirect("*/*/");
			}
	}

	public function newAction()
	{

	$this->_title($this->__("Dirvermanagement"));
	$this->_title($this->__("Driver"));
	$this->_title($this->__("New Item"));

    $id   = $this->getRequest()->getParam("user_id");
	$model  = Mage::getModel("dirvermanagement/driver")->load($id);

	$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
	if (!empty($data)) {
		$model->setData($data);
	}

	Mage::register("driver_data", $model);

	$this->loadLayout();
	$this->_setActiveMenu("dirvermanagement/driver");

	$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

	$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Driver Manager"), Mage::helper("adminhtml")->__("Driver Manager"));
	$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Driver Description"), Mage::helper("adminhtml")->__("Driver Description"));


	$this->_addContent($this->getLayout()->createBlock("dirvermanagement/adminhtml_driver_edit"))->_addLeft($this->getLayout()->createBlock("dirvermanagement/adminhtml_driver_edit_tabs"));

	$this->renderLayout();

	}
	public function saveAction()
	{
		$post_data=$this->getRequest()->getPost();
		//echo "<pre>";print_r($post_data);
		$now = date('Y-m-d H:i:s');
			if ($post_data) {

				try {
					if(!$this->getRequest()->getParam("userid"))
					{
						//echo 'if';die;
						$user = Mage::getModel('admin/user')
						->setData($post_data)->save();

				        $role = Mage::getModel("admin/role");
				        $role->setParent_id(5);
				        $role->setTree_level(2);
				        $role->setRole_type('U');
				        $role->setUserId($user->getUserId());
				        $role->setRoleName($user->getFirstname());
				        $role->save();
						$model = Mage::getModel("dirvermanagement/driver")
						->setMobile($post_data['mobile'])
						->setUniqueId($post_data['unique_id'])
						->setUserid($user->getUserId())
						->setLongitude($post_data['longitude'])
						->setLatitude($post_data['latitude'])
						->setUpdatedAt($now)
						->save();
					}
					else{
						//echo 'else';die;
						//$adminmodel = Mage::getModel("admin/user")
						//->addData($post_data)
						//->setUserId($this->getRequest()->getParam("userid"))
						//->save();
						$id = $this->getRequest()->getParam("userid");
						
						$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
						$updateQue = 'UPDATE `admin_user` SET username="'.$post_data['username'].'",firstname="'.$post_data['firstname'].'",lastname="'.$post_data['lastname'].'",email="'.$post_data['email'].'",password="'.md5($post_data['password']).'",is_active="'.$post_data['is_active'].'" WHERE user_id='.$id;
						
						$db_write1->query($updateQue);


						$model = Mage::getModel("dirvermanagement/driver")
						->addData($post_data)
						->setUserid($this->getRequest()->getParam("userid"));
						$model->setUpdatedAt($now);
						$model->save();
					}

					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Driver was successfully saved"));
					Mage::getSingleton("adminhtml/session")->setDriverData(false);

					if ($this->getRequest()->getParam("back")) {
						$this->_redirect("*/*/edit", array("user_id" => $model->getUserId()));
						return;
					}
					$this->_redirect("*/*/");
					return;
				}
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					Mage::getSingleton("adminhtml/session")->setDriverData($this->getRequest()->getPost());
					$this->_redirect("*/*/edit", array("user_id" => $this->getRequest()->getParam("user_id")));
				return;
				}

			}
			$this->_redirect("*/*/");
	}



	public function deleteAction()
	{
			if( $this->getRequest()->getParam("user_id") > 0 ) {
				try {
					$model = Mage::getModel("dirvermanagement/driver");
					$model->setId($this->getRequest()->getParam("user_id"))->delete();
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
					$this->_redirect("*/*/");
				}
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					$this->_redirect("*/*/edit", array("user_id" => $this->getRequest()->getParam("user_id")));
				}
			}
			$this->_redirect("*/*/");
	}


	public function massRemoveAction()
	{
		// echo "<pre>";print_r($this->getRequest()->getPost());exit;
		try {
			$ids = $this->getRequest()->getPost('ids', array());
			foreach ($ids as $id) {
                  $model = Mage::getModel("dirvermanagement/driver")->load($id,'userid');
				  $model->setId($model->getId())->delete();
				  $user = Mage::getModel('admin/user');
				  $user->setUserId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Export order grid to CSV format
	 */
	public function exportCsvAction()
	{
		$fileName   = 'driver.csv';
		$grid       = $this->getLayout()->createBlock('dirvermanagement/adminhtml_driver_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	}
	/**
	 *  Export order grid to Excel XML format
	 */
	public function exportExcelAction()
	{
		$fileName   = 'driver.xml';
		$grid       = $this->getLayout()->createBlock('dirvermanagement/adminhtml_driver_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}

	public function assigndriverAction(){
		$post = $this->getRequest()->getParams();
		$orderid = $post['order_id'];
		$driverid = $post['assign_driver'];
		$order  = Mage::getModel('sales/order')->load($orderid);
		try{
			if($order->getId()){
				$order->setDriverId($driverid);
				$order->save();
			}
			if($order->getOrderStatus() == 'dispatched'){
				// Islam Elgarhy Send Disspatched Message
				if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) 
				{
					$customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
					if($telephone = $customer->getData("phone")){
						$driver_id = $order->getDriverId();
						$driverdata = Mage::getModel("dirvermanagement/driver")->load($driver_id,'userid')->getData();
						$url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
						$order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($order->getIncrementId());        
						$order_url = Mage::getUrl($order_urls, array('_secure' => true));
						$username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
						$password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
						$msisdn = '965'.$telephone;
						$unicode_msg = str_replace('{{order link}}', $order_url . ' Driver Phone ' . $driverdata[mobile] , Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
						$post_body = $this->seven_bit_sms( $username, $password, $unicode_msg, $msisdn );
						$result =  $this->send_message( $post_body, $url );
						if($result['success']){
							//$data->setIsCustomerNotify('1');
						}else{            
							//$data->setIsCustomerNotify('0');
						}
					} 
				 
				}
			}
			
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Driver has been assigned . "));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirectReferer();
	}
	public function assignstatusAction(){
		$post = $this->getRequest()->getParams();
		$orderid = $post['order_id'];
		$assign_status = $post['assign_status'];
		
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];

        }
		$order  = Mage::getModel('sales/order')->load($orderid);
		$comment = '#'.$order->getIncrementId()." Order status has been changed to ".$customstatus[$assign_status];
		try{
			if($order->getId()){
				if($assign_status){
				$order->setOrderStatus($assign_status);
        		$order->addStatusHistoryComment($comment)
		            ->setIsVisibleOnFront(true)
		            ->setIsCustomerNotified(false);
				$order->save();
				// Create Shipment 
				if($order->getOrderStatus() == 'delivered') {
		        	$this->Creteshipment($order);
		        }
		        // Cancel an order
		        if($order->getOrderStatus() == 'returned' || $order->getOrderStatus() == 'canceled') {
		        	$this->Cancelorder($order);
		        }


				//Dispatch and send message
			
		        if($order->getOrderStatus() == 'dispatched'){
		        	/*if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
			            if($telephone = $order->getBillingAddress()->getTelephone()){
			                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
			                $order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($order->getIncrementId());        
			                $order_url = Mage::getUrl($order_urls, array('_secure' => true));
			                Mage::log($order_url , null, 'mylog3.log');
			                $fields = array(
			                    'username' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname'),
			                    'password' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd'),
			                    'customerid' => '361',
			                    'sendertext' => 'HiNet GCC',
			                    'messagebody' => str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage')),
			                    'recipientnumbers' => '965'.$telephone, // '96550618808',
			                    'defdate' => '', //Mage::getModel('core/date')->date('Y-m-d'),
			                    'isblink' => 'false',
			                    'isflash' => 'false'
			                );
			                $field = http_build_query($fields); // encode array to POST string
			                //Mage::log(print_r($field, 1), null, 'mylog3.log');       
			                $post = curl_init();
			                curl_setopt($post, CURLOPT_URL, $url);
			                curl_setopt($post, CURLOPT_POST, 1);
			                curl_setopt($post, CURLOPT_POSTFIELDS, $field);
			                curl_setopt($post, CURLOPT_USERAGENT, 'Mozilla/5.0');
			                curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
			                $result = curl_exec($post); 
			                // for debugging
			                $sxml = new SimpleXMLElement($result);
			                $Result = $this->xml2array($sxml);
			            
			                if($Result['Result'] == 'true'){
			                	Mage::log($order->getIncrementId(), null, 'mylog3.log');
			                	Mage::log('Success', null, 'mylog3.log');
			                    //$data->setIsCustomerNotify('1');
			                }else{
			                	Mage::log($order->getIncrementId(), null, 'mylog3.log');
			                	Mage::log('Failed', null, 'mylog3.log');         
			                    //$data->setIsCustomerNotify('0');
			                }
			                curl_close($post);
			                Mage::log(print_r($result, 1) , null, 'mylog3.log');
			            }      
					}*/
  					// Islam Elgarhy Send Disspatched Message
					/*if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) 
					{
						
						$customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
						if($telephone = $customer->getData("phone")){
							$url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
							$order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($order->getIncrementId());        
							$order_url = Mage::getUrl($order_urls, array('_secure' => true));
							$username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
							$password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
							$msisdn = '965'.$telephone;
							$unicode_msg = str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
							$post_body = $this->seven_bit_sms( $username, $password, $unicode_msg, $msisdn );
							$result =  $this->send_message( $post_body, $url );
							if($result['success']){
								//$data->setIsCustomerNotify('1');
							}else{            
								//$data->setIsCustomerNotify('0');
							}
						}     
					
					}  */   
		        }


				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Order status has been changed . "));
				}
				else{
					Mage::getSingleton("adminhtml/session")->addError('Select Any Status..');
				}
			}			
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}


		$this->_redirectReferer();
	}

	public function xml2array($xml){
        $arr = array();

        foreach ($xml as $element)
        {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e))
            {
                $arr[$tag] = $element instanceof SimpleXMLElement ? xml2array($element) : $e;
            }
            else
            {
                $arr[$tag] = trim($element);
            }
        }

        return $arr;
    }
    
	public function updateDriverAction(){
	    $fieldId = (int) $this->getRequest()->getParam('id');
	    $driverId = $this->getRequest()->getParam('driver_id');
	    if ($fieldId) {
	        $model = Mage::getModel('sales/order')->load($fieldId);
	        $model->setDriverId($driverId);
			$model->save();
			
			if($model->getOrderStatus() == 'dispatched'){
				// Islam Elgarhy Send Disspatched Message
				if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) 
				{
					$customer = Mage::getModel('customer/customer')->load($model->getData('customer_id'));
					if($telephone = $customer->getData("phone")){
						$driver_id = $model->getDriverId();
						$driverdata = Mage::getModel("dirvermanagement/driver")->load($driver_id,'userid')->getData();
						$url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
						$order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($model->getIncrementId());        
						$order_url = Mage::getUrl($order_urls, array('_secure' => true));
						$username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
						$password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
						$msisdn = '965'.$telephone;
						$unicode_msg = str_replace('{{order link}}', $order_url . ' Driver Phone ' . $driverdata[mobile] , Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
						$post_body = $this->seven_bit_sms( $username, $password, $unicode_msg, $msisdn );
						$result =  $this->send_message( $post_body, $url );
						if($result['success']){
							//$data->setIsCustomerNotify('1');
						}else{            
							//$data->setIsCustomerNotify('0');
						}
					} 
				 
				}    
			}
	    }
	}
	public function updateStatusAction(){
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];
        }
	    $fieldId = (int) $this->getRequest()->getParam('id');
	    $assign_status = $this->getRequest()->getParam('assign_status');
	    if ($fieldId) {
	        $model = Mage::getModel('sales/order')->load($fieldId);
			$comment = '#'.$model->getIncrementId()." Order status has been changed to ".$customstatus[$assign_status];
	        $model->setOrderStatus($assign_status);
        	$model->addStatusHistoryComment($comment)
	            ->setIsVisibleOnFront(true)
	            ->setIsCustomerNotified(false);
	        $model->save();
	        // Create Shipment 
	        if($model->getOrderStatus() == 'delivered'){
	        	$this->Creteshipment($model);
	        }
	        // Cancel an order
	        if($model->getOrderStatus() == 'returned' || $model->getOrderStatus() == 'canceled'){
	        	$this->Cancelorder($model);
	        }
			//Dispatch and send message
	        if($model->getOrderStatus() == 'dispatched'){
	        	/*if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
            
		            if($telephone = $model->getBillingAddress()->getTelephone()){
		                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');

		                $order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($model->getIncrementId());        
		                $order_url = Mage::getUrl($order_urls, array('_secure' => true));
		                Mage::log($order_url , null, 'mylog3.log');
		                $fields = array(
		                    'username' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname'),
		                    'password' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd'),
		                    'customerid' => '361',
		                    'sendertext' => 'HiNet GCC',
		                    'messagebody' => str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage')),
		                    'recipientnumbers' => '965'.$telephone, // '96550618808',
		                    'defdate' => '', //Mage::getModel('core/date')->date('Y-m-d'),
		                    'isblink' => 'false',
		                    'isflash' => 'false'
		                );
		                $field = http_build_query($fields); // encode array to POST string
		                //Mage::log(print_r($field, 1), null, 'mylog3.log');       
		                $post = curl_init();
		                curl_setopt($post, CURLOPT_URL, $url);
		                curl_setopt($post, CURLOPT_POST, 1);
		                curl_setopt($post, CURLOPT_POSTFIELDS, $field);
		                curl_setopt($post, CURLOPT_USERAGENT, 'Mozilla/5.0');
		                curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
		                $result = curl_exec($post); 
		                // for debugging
		                $sxml = new SimpleXMLElement($result);
		                $Result = $this->xml2array($sxml);
		            
		                if($Result['Result'] == 'true'){
		                	Mage::log($model->getIncrementId(), null, 'mylog3.log');
		                	Mage::log('Success', null, 'mylog3.log');
		                    //$data->setIsCustomerNotify('1');
		                }else{
		                	Mage::log($model->getIncrementId(), null, 'mylog3.log');
		                	Mage::log('Failed', null, 'mylog3.log');         
		                    //$data->setIsCustomerNotify('0');
		                }
		                curl_close($post);
		                Mage::log(print_r($result, 1) , null, 'mylog3.log');
		            }      
				}*/
					// Islam Elgarhy Send Disspatched Message
					/*if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) 
					{
						$customer = Mage::getModel('customer/customer')->load($model->getData('customer_id'));
						if($telephone = $customer->getData("phone")){
							$url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
							$order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($model->getIncrementId());        
							$order_url = Mage::getUrl($order_urls, array('_secure' => true));
							$username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
							$password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
							$msisdn = '965'.$telephone;
							$unicode_msg = str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
							$post_body = $this->seven_bit_sms( $username, $password, $unicode_msg, $msisdn );
							$result =  $this->send_message( $post_body, $url );
							if($result['success']){
								//$data->setIsCustomerNotify('1');
							}else{            
								//$data->setIsCustomerNotify('0');
							}
						}      
					}   */
	        }
	    }
	}
	
	public function changedeliverydateAction(){    	
    	$post = $this->getRequest()->getParams();
		$orderid = $post['order_id'];
		$ShippingArrivalDate = $post['delivery_date'];
		$ShippingArrivalTime = $post['shipping_arrival_time'];

		$slot = Mage::getModel('deliverydate/deliverydate')->load($ShippingArrivalTime);
        $tim_slot = $slot->getData('fromtime')." - ".$slot->getData('totime');
        $desiredArrivalDate = $ShippingArrivalDate . " " . $tim_slot;
        $delivery_date_ori = $ShippingArrivalDate." ".$slot->getData('totime');
        $delivery_date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s',$delivery_date_ori);
		$order  = Mage::getModel('sales/order')->load($orderid);
		try{
			if($order->getId()){
				$order->setShippingArrivalDate($desiredArrivalDate);
				$order->setShippingArrivalTimeSlot($ShippingArrivalTime);
				$order->setShippingDeliveryDate($delivery_date);
				$order->save();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Delivery Date has been Changed . "));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirectReferer();

    }

    public function Creteshipment($model){
        if($model->getOrderStatus() == 'delivered'){
        	try {
			    if($model->canShip()) {
			        $shipmentid = Mage::getModel('sales/order_shipment_api')
			                        ->create($model->getIncrementId(), array());
			        //$ship = Mage::getModel('sales/order_shipment_api')
			                        //->addTrack($model->getIncrementId(), array());
			    }
			}catch (Mage_Core_Exception $e) {
			 	Mage::logException($e);
			}
        }
        return $this;
    }

    public function Cancelorder($model){
        if($model->getOrderStatus() == 'returned' || $model->getOrderStatus() == 'canceled'){
        	if ($model->canCancel()) {
			    try {
			        $model->cancel();
			        //$model->getStatusHistoryCollection(true);
			        $model->setOrderStatus('canceled');
			        $model->save();
			    } catch (Exception $e) {
			        Mage::logException($e);
			    }
			}
        }
        return $this;
	}
	// Test SMS Islam Elgarhy 


	function send_message ( $post_body, $url ) {
        /*
        * Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn it into in a
        * multipart formpost, which is not supported:
        */
      
        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
        // Allowing cUrl funtions 20 second to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Waiting 20 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
      
        $response_string = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );
      
        $sms_result = array();
        $sms_result['success'] = 0;
        $sms_result['details'] = '';
        $sms_result['transient_error'] = 0;
        $sms_result['http_status_code'] = $curl_info['http_code'];
        $sms_result['api_status_code'] = '';
        $sms_result['api_message'] = '';
        $sms_result['api_batch_id'] = '';
      
        if ( $response_string == FALSE ) {
          $sms_result['details'] .= "cURL error: " . curl_error( $ch ) . "\n";
        } elseif ( $curl_info[ 'http_code' ] != 200 ) {
          $sms_result['transient_error'] = 1;
          $sms_result['details'] .= "Error: non-200 HTTP status code: " . $curl_info[ 'http_code' ] . "\n";
        }
        else {
          $sms_result['details'] .= "Response from server: $response_string\n";
          $api_result = explode( '|', $response_string );
          $status_code = $api_result[0];
          $sms_result['api_status_code'] = $status_code;
          $sms_result['api_message'] = $api_result[1];
          if ( count( $api_result ) != 3 ) {
            $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count( $api_result );
          } else {
            if ($status_code == '0') {
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
              $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
            }
            else if ($status_code == '1') {
              # Success: scheduled for later sending.
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
            }
            else {
              $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
            }
      
      
      
      
      
          }
        }
        curl_close( $ch );
      
        return $sms_result;
      }
      

      function seven_bit_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
        'username' => $username,
        'password' => $password,
        'message'  => $this->character_resolve( $message ),
        'msisdn'   => $msisdn,
        'allow_concat_text_sms' => 0, # Change to 1 to enable long messages
        'concat_text_sms_max_parts' => 2
        );
      
        return $this->make_post_body($post_fields);
      }

      function unicode_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
        'username' => $username,
        'password' => $password,
        'message'  => $this->string_to_utf16_hex( $message ),
        'msisdn'   => $msisdn,
        'dca'      => '16bit',
        'allow_concat_text_sms' => 1
        );
      
        return $this->make_post_body($post_fields);
      }

      function make_post_body($post_fields) {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
          $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach( $post_fields as $key => $value ) {
          $post_body .= urlencode( $key ).'='.urlencode( $value ).'&';
        }
        $post_body = rtrim( $post_body,'&' );
      
        return $post_body;
      }
      
  
      
      /*
      * Unique ID to eliminate duplicates in case of network timeouts - see
      * EAPI documentation for more. You may want to use a database primary
      * key. Warning: sending two different messages with the same
      * ID will result in the second being ignored!
      *
      * Don't use a timestamp - for instance, your application may be able
      * to generate multiple messages with the same ID within a second, or
      * part thereof.
      *
      * You can't simply use an incrementing counter, if there's a chance that
      * the counter will be reset.
      */
      function make_stop_dup_id() {
        return 0;
      }
      
      function string_to_utf16_hex( $string ) {
        return bin2hex(mb_convert_encoding($string, "UTF-16", "UTF-8"));
      }
      
      function character_resolve($body) {
        $special_chrs = array(
        'Δ'=>'0xD0', 'Φ'=>'0xDE', 'Γ'=>'0xAC', 'Λ'=>'0xC2', 'Ω'=>'0xDB',
        'Π'=>'0xBA', 'Ψ'=>'0xDD', 'Σ'=>'0xCA', 'Θ'=>'0xD4', 'Ξ'=>'0xB1',
        '¡'=>'0xA1', '£'=>'0xA3', '¤'=>'0xA4', '¥'=>'0xA5', '§'=>'0xA7',
        '¿'=>'0xBF', 'Ä'=>'0xC4', 'Å'=>'0xC5', 'Æ'=>'0xC6', 'Ç'=>'0xC7',
        'É'=>'0xC9', 'Ñ'=>'0xD1', 'Ö'=>'0xD6', 'Ø'=>'0xD8', 'Ü'=>'0xDC',
        'ß'=>'0xDF', 'à'=>'0xE0', 'ä'=>'0xE4', 'å'=>'0xE5', 'æ'=>'0xE6',
        'è'=>'0xE8', 'é'=>'0xE9', 'ì'=>'0xEC', 'ñ'=>'0xF1', 'ò'=>'0xF2',
        'ö'=>'0xF6', 'ø'=>'0xF8', 'ù'=>'0xF9', 'ü'=>'0xFC',
        );
      
        $ret_msg = '';
        if( mb_detect_encoding($body, 'UTF-8') != 'UTF-8' ) {
          $body = utf8_encode($body);
        }
        for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
          $c = mb_substr( $body, $i, 1, 'UTF-8' );
          if( isset( $special_chrs[ $c ] ) ) {
            $ret_msg .= chr( $special_chrs[ $c ] );
          }
          else {
            $ret_msg .= $c;
          }
        }
        return $ret_msg;
      }
     

    // End Test SMS 
}
