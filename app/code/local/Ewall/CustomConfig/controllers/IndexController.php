<?php
class Ewall_CustomConfig_IndexController extends Mage_Core_Controller_Front_Action{
    public function saveAction(){
    	try{
			$getRequest = $this->getRequest()->getPost();
			$order_Id = Mage::helper('core')->urlDecode($getRequest['order_id']);

			$guest_collection = Mage::getModel('customconfig/guest');
			$guest_collection->setGuestName($getRequest['guest_name']);
			$guest_collection->setGuestEmail($getRequest['guest_email']);
			$guest_collection->setSurveyOption($getRequest['survey_option']);
			$guest_collection->setSurveyMessage($getRequest['survey_message']);
			$guest_collection->setOrderId($order_Id);

			$custom_email_template = Mage::getStoreConfig('customconfig_options/section_four/surveyemailtemplate');    
	        $email = Mage::getStoreConfig('customconfig_options/section_four/email');
	        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault($custom_email_template);
	        $emailTemplateVariables = $guest_collection->getData();
	        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
	        $emailTemplate_sent = $emailTemplate->send($email,'', $emailTemplateVariables);

	        if($emailTemplate_sent){
	        	$guest_collection->setIsMailSent(1);
	        }else{
	        	$guest_collection->setIsMailSent(0);
	        }
	        
			$guest_collection->save();
			
			$order = Mage::getModel('sales/order')->load($order_Id, 'increment_id');
			$order->setIsSurvey($getRequest['survey_option']);
        	$order->save();

        	$order_history = Mage::getModel('sales/order_status_history')->load($order->getEntityId(), 'parent_id');
			$order_history->setIsSurvey($getRequest['survey_option']);
        	$order_history->save();

        	$order_grid = Mage::getModel('sales/order_grid')->load($order->getEntityId(), 'parent_id');
			$order_grid->setIsSurvey($getRequest['survey_option']);
        	$order_grid->save();

			Mage::getSingleton('core/session')->addSuccess('Thank you for survey us.');		
		}
		catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
	$return = 'sales/guest/view/order_id/'.$getRequest['order_id'];
	$this->_redirectUrl(Mage::getUrl($return, array('_secure' => true)));
    }


    public function updatestatusAction() {
    	try{
	    	$getRequest = $this->getRequest()->getPost();
	    	$status = $getRequest['status'];
	    	$item_id = $getRequest['item_id'];
	    	$order = Mage::getModel('sales/order_item')->load($item_id, 'item_id');
			$order->setItemStatus($status);
			$order->save();
			echo "Sucessfully updated ths Item..";
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
    }

    public function customerstatusAction(){
    	$getRequest = $this->getRequest()->getParam('order_id');
		$order_Id = Mage::helper('core')->urlDecode($getRequest);
	    	
    	try{
	  		$order = Mage::getModel('sales/order')->load($order_Id, 'increment_id');
	    	$order->setStatus('canceled'); //order_status
			$order->setOrderStatus('canceled');
			$order->save();
			Mage::getSingleton('core/session')->addSuccess('Sucessfully canceled the Order..');
			
		}
		catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
		$return = 'sales/guest/view/order_id/'.$getRequest;
		$this->_redirectUrl(Mage::getUrl($return, array('_secure' => true)));
    }

    public function loginisvalidAction(){
    	try{
    		$this->loadLayout();
        	$this->renderLayout();
        	$username = $this->getRequest()->getParam('username');
        	$password = $this->getRequest()->getParam('password');
        	$deviceid = $this->getRequest()->getParam('deviceid');

        	Mage::getSingleton('core/session', array('name' => 'adminhtml'));
			$userbyname = Mage::getModel('admin/user')->loadByUsername($username);
			if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
			  Mage::getSingleton('adminhtml/url')->renewSecretUrls();
			}
			$user = Mage::getModel('admin/user')->load($userbyname->getId());
			$user_id = $user->getId();

			////log
			$getRequest = $this->getRequest()->getParams();
			
			Mage::log(print_r($getRequest,1),null,'loginisvalidlog.log');

			if(($user->getId())>=1)
			{	
			    $session = Mage::getSingleton('admin/session');
			    $dbpassword = $user->getData('password');
			    $a = Mage::helper('core')->validateHash($password, $dbpassword);

			    if($a)
			    {			    	
					$session->setIsFirstVisit(true);
					$session->setUser($user);
					$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
					Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));
					if ($session->isLoggedIn() && $user->getUserId()) {
					  	$users['userid'] = $user->getUserId();
					  	$users['valid'] = 1;
					  	$collection = Mage::getModel('dirvermanagement/driver')->load($users['userid'], 'userid');

					  	if($collection->getUserid()){
					  		$collection->setUniqueId($deviceid);
							$collection->setUpdatedAt($updated_at);
							$collection->save();
					  	}else{
					  		$collection = Mage::getModel('dirvermanagement/driver');
							$collection->setUserid($users['userid']);
							$collection->setUniqueId($deviceid);
							$collection->setUpdatedAt($updated_at);
							$collection->save();
					  	}
					  	
					}else{
					    $users['valid'] = 0;
					}			       					
			    }
			    else
			    {
			    	$users['valid'] = 0;
			    }
			    
			}
			else
			{
				$users['valid'] = 0;
			    Mage::getSingleton('core/session')->addError('User Name: False');
			}	
    	}
		catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($users));
    }

    public function isvalidAction(){
	    	$this->loadLayout();
        	$this->renderLayout();
        	$userid = $this->getRequest()->getParam('userid');
        	$valid = $this->getRequest()->getParam('valid');
	        Mage::getSingleton('core/session', array('name' => 'adminhtml'));
	       
			////log
			$getRequest = $this->getRequest()->getParams();			
			Mage::log(print_r($getRequest,1),null,'logvalidlog.log');

			if(isset($userid)){
			$user = Mage::getModel('admin/user')->load($userid); 
				if($user->getUsername()){
				if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
				Mage::getSingleton('adminhtml/url')->renewSecretUrls();
				}
				$session = Mage::getSingleton('admin/session');
				$session->setIsFirstVisit(true);
				$session->setUser($user);
				$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
				Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));
					if ($session->isLoggedIn()) {
						//echo "Logged in";
						//$response = array('status'=>'true');
						Mage::getSingleton('core/session')->addSuccess('Logged in Sucessfully..');
						$url = $session->getUser()->getStartupPageUrl();
					} else {
						//echo "Not Logged";
						//$response = array('status'=>'false');
						Mage::getSingleton('core/session')->addError('Not Logged');
					}
				}else{
					//echo "This id not found try someother id";
					//$response = array('status'=>'false');
					Mage::getSingleton('core/session')->addError('This id not found try someother id');
				}
			} else {
				//echo "Please provide the id";
				//$response = array('status'=>'false');
				Mage::getSingleton('core/session')->addError('Please provide the id');
		}
		//print_r($response);
		/*$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
		$this->getResponse()->setBody(json_encode($response));*/
		Mage::log(print_r($this->getRequest()->getParams(),1),null,'isvalid.log');
		$this->_redirect($url);
	}
}