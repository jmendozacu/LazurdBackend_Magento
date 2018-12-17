<?php
class Ewall_CustomConfig_DriverController extends Mage_Core_Controller_Front_Action{
    public function saveAction(){
    	try{
			$userid = $this->getRequest()->getParam('userid');
        	$lon = $this->getRequest()->getParam('lon');
        	$lat = $this->getRequest()->getParam('lat');
			$updated_at = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			if($userid){
				$model = Mage::getModel('dirvermanagement/driver');
				if($model->load($userid, 'userid')){
					$model->setLongitude($lon);
					$model->setLatitude($lat);
					$model->setUpdatedAt($updated_at);					
				}
				else{
					$model->setLongitude($lon);
					$model->setLatitude($lat);
					$model->setUpdatedAt($updated_at);
				}
				$model->save();
			}

		Mage::getSingleton('core/session')->addSuccess('Driver Added Successfully');
		Mage::log(print_r($this->getRequest()->getParams(),1),null,'driver_save.log');
		}
		catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->message());
		}
    }
}