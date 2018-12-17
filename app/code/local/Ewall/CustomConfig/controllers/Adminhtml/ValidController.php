<?php
class Ewall_CustomConfig_Adminhtml_ValidController extends Mage_Adminhtml_Controller_Action{
	    public function isvalidAction(){
	    	/*$this->loadLayout();
        	$this->renderLayout();*/
        	$userid = $this->getRequest()->getParam('userid');
        	$valid = $this->getRequest()->getParam('valid');
	        Mage::getSingleton('core/session', array('name' => 'adminhtml'));
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
						$response = array('status'=>'true');
					} else {
						//echo "Not Logged";
						$response = array('status'=>'false');
					}
				}else{
					//echo "This id not found try someother id";
					$response = array('status'=>'false');
				}
			} else {
				//echo "Please provide the id";
				$response = array('status'=>'false');
			}
		//print_r($response);
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
		$this->getResponse()->setBody(json_encode($response));
	}
}