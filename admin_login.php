<?php
require_once 'app/Mage.php';
umask(0);
$app = Mage::app();
Mage::getSingleton('core/session', array('name' => 'adminhtml'));
	
	//log
	//Mage::log(print_r($_GET,1),null,'driver_login.log');
	$users = array();
	if(isset($_GET['user_id'])) {
	$user = Mage::getModel('admin/user')->load($_GET['user_id']); 
		if($user->getUsername()){
		$user->setIsActive(true);
		if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
		Mage::getSingleton('adminhtml/url')->renewSecretUrls();
		}
		$session = Mage::getSingleton('admin/session');
		$session->setIsFirstVisit(true);
		$session->setUser($user);
		$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
		Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));
			if ($session->isLoggedIn()) {
				echo "Logged in";
				$url_index = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'admin';
				$url = str_replace("admin_login","index",$url_index);
				header('Location: '.$url);
				exit;
			} else {
				//echo "Not Logged";
				$users['valid'] = 0;
			}
		}else{
			//echo "This id not found try someother id";
			$users['valid'] = 0;
		}
	} else {
		//echo "Please provide the id";
		$users['valid'] = 0;
	}
	echo json_encode($users);