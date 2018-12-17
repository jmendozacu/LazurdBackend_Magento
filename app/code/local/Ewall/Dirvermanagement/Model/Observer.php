<?php
    class Ewall_Dirvermanagement_Model_Observer{

        public function saveDriverUsers(Varien_Event_Observer $observer)
        {
            $observer->getEvent();
            $data = $observer->getEvent()->getObject();
            $admin_user_session = Mage::getSingleton('admin/session');
            $user_id = $admin_user_session->getUser()->getUserId();
            $user_id = Mage::app()->getRequest()->getParam('user_id');
            $role_data = Mage::getModel('admin/user')->load($user_id)->getRole()->getData();
            $UserRoles = $data->getData('user_roles'); //$role_data['user_roles'];
            $now = date('Y-m-d H:i:s');
            if ($role_data['role_name'] == 'Driver'){
                $getCollections = Mage::getModel('dirvermanagement/driver')->getCollection()->addFieldToFilter('userid',$user_id);
                if($getCollections->getData()){
                    $getCollection = Mage::getModel('dirvermanagement/driver')->load($user_id, 'userid');
                    $getCollection->setUserid($user_id);
                    $getCollection->setUpdatedAt($now);
                    $getCollection->save();

                }else{
                    $getCollection = Mage::getModel('dirvermanagement/driver');
                    $getCollection->setUserid($user_id);
                    $getCollection->setUpdatedAt($now);
                    $getCollection->save();
                }
            }
        }

        public function createDrivers(Varien_Event_Observer $observer)
        {
            $url = Mage::app()->getRequest()->getRouteName().'_'. Mage::app()->getRequest()->getControllerName().'_'. Mage::app()->getRequest()->getActionName();
            $user_id = Mage::app()->getRequest()->getParam('user_id');
            $controller = $observer->getControllerAction();
            if($url == 'adminhtml_permissions_user_save' && !$user_id){
                $post = $controller->getRequest()->getPost();
                $role_data = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('email' , $post['email'])->getFirstItem();
                $user_id = $role_data ->getUserId();
                $role_data = Mage::getModel('admin/user')->load($user_id)->getRole()->getData();
                $now = date('Y-m-d H:i:s');
                if ($role_data['role_name'] == 'Driver'){
                    $getCollections = Mage::getModel('dirvermanagement/driver')->getCollection()->addFieldToFilter('userid',$user_id);
                    if($getCollections->getData()){
                        $getCollection = Mage::getModel('dirvermanagement/driver')->load($user_id, 'userid');
                        $getCollection->setUserid($user_id);
                        $getCollection->setUpdatedAt($now);
                        $getCollection->save();

                    }else{
                        $getCollection = Mage::getModel('dirvermanagement/driver');
                        $getCollection->setUserid($user_id);
                        $getCollection->setUpdatedAt($now);
                        $getCollection->save();
                    }
                }
            }
        }
        public function handleOrderCollectionLoadBefore($observer)
        {
            $admin_user_session = Mage::getSingleton('admin/session');
            $adminuserId = $admin_user_session->getUser()->getUserId();
            $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
            $kitchen = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
            $role_id = $role_data->getRoleId();
            if($role_id != 1){
                $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');
                if($userrole_status->getViewdStatus()){
                    $role_status = explode(',', $userrole_status->getViewdStatus());
                    if($userrole_status->getId()){
                        $collection = $observer->getOrderGridCollection();
                        if ($collection){
                            $collection->addFieldToFilter('order_status', array('in' => $role_status));
                            if($role_id == $kitchen)
                            {
                                $collection->addFieldToFilter('kitchen_user_ids', array('finset' => $adminuserId));
                            }
                        }

                    }
                }
            }
        }
        public function cancelCustomStatus($observer)
        {            
            $order = $observer->getEvent()->getOrder();
            if($order){
                $order->setOrderStatus('canceled')->save();
            }
        }
    }
?>
