<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

 //NEW 2018
class Magestore_Webpos_Model_Api2_Staff_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**#@+
     *  Action types
     */
    const ACTION_TYPE_ENTITY_LOGIN = 'entity_login';
    const ACTION_TYPE_ENTITY_ACTIVEDISCOUNT = 'entity_activediscount';
    const ACTION_TYPE_ENTITY_LOGOUT = 'entity_logout';
    const ACTION_TYPE_ENTITY_CHANGEPASSWORD = 'entity_changepassword';
	 //<!--Edit by 28122017 Islam ELgarhy -->
    const OPERATION_GET_STAFF_LIST = 'get';
    const OPERATION_GET_SALESSTAFF_LIST = 'entity_getsales';
	 //<!--Edit by 28122017 Islam ELgarhy -->
    /**#@-*/

    /**
     * Retrieve information about staff
     *
     * @throws Mage_Api2_Exception
     * @return array|bool
     */
    protected function login()
    {
        $requestData = $this->getRequest()->getBodyParams();

        $username = $requestData['staff']['username'];
        $password = $requestData['staff']['password'];
        $store = $requestData['store'];

        if ($username && $password) {
            try {
                $resultLogin = Mage::helper('webpos/permission')->login($username, $password,$store);
                if ($resultLogin != 0) {
                    $data = array();
                    $data['current_store_id'] = $store;
                    $data['staff_id'] = $resultLogin;
                    Mage::getSingleton("core/session")->renewSession();
                    $data['session_id'] = Mage::getSingleton("core/session")->getEncryptedSessionId();
                    $data['logged_date'] = strftime('%Y-%m-%d %H:%M:%S', Mage::getModel('core/date')->gmtTimestamp());

                    $webpossession = Mage::getModel('webpos/user_webpossession');
                    $webpossession->setData($data);
                    $cashDrawers = $webpossession->getAvailableCashDrawer();
                    if(!empty($cashDrawers) && count($cashDrawers) == 1){
                        foreach ($cashDrawers as $id => $name){
                            $webpossession->setCurrentTillId($id);
                        }
                    }
                    $insertid = $webpossession->save()->getId();
                    $tillId = $webpossession->getCurrentTillId();

                    if(isset($insertid)){
                        $response = array(
                            'webpos_config' => Mage::helper('webpos')->getWebposConfig($data['session_id']),
                            'webpos_data' => Mage::getModel('webpos/dataManager')->getWebposData($data['session_id']),
                            'session_id' => $data['session_id'],
                            'location_id' => $webpossession->getStaffLocationId(),
                            'available_tills' => $cashDrawers,
                            'store_url' => Mage::getModel('core/store')->load($data['current_store_id'])->getUrl('webpos/index/index', array('_secure' => true))
                          
                        );
                        if(!empty($tillId)) {
                            $response['till_id'] = $tillId;
                        }
                        return $response;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }catch (\Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     * Retrieve information about staff
     *
     * @throws Mage_Api2_Exception
     * @return array|bool
     */

    protected function activediscount()
    {
        $requestData = $this->getRequest()->getBodyParams();
        $username = $requestData['staff']['username'];
        $password = $requestData['staff']['password'];
        $store = $requestData['store'];

        if ($username && $password) 
        {
            try {
                $resultLogin = Mage::helper('webpos/permission')->login($username, $password,$store);
                if ($resultLogin != 0) 
                {
                    $resourceAccess = array();
                        $staffId = $resultLogin;
                        $staffModel = Mage::getModel('webpos/user')->load($staffId);
                        $roleId = $staffModel->getRoleId();
                        $roleModel =  Mage::getModel('webpos/role')->load($roleId);
                        $authorizeRuleCollection = explode(',',$roleModel->getPermissionIds());
                        $roleOptionsArray = $roleModel->getOptionArray();
                        foreach ($authorizeRuleCollection as $authorizeRule) {
                            if (array_key_exists($authorizeRule,$roleOptionsArray))
                            {
                                $resourceAccess[] = $roleOptionsArray[$authorizeRule];
                            }
                        }
                          /*Edit by 24122017 Islam ELgarhy*/
                    $response = array(
                            'staffResourceAccess' =>$resourceAccess,
                            'Staff' =>$staffId,
                          
                    ); 
                      /*Edit by 24122017 Islam ELgarhy*/
                    return $response;
                } 
                else 
                {
                    return false;
                }
            }
        
            catch (Mage_Core_Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }
            catch (\Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     *
     * @return string
     */
    public function logout()
    {
        $sessionId = $this->getRequest()->getParam('session');
        $sessionLoginCollection = Mage::getModel('webpos/user_webpossession')->getCollection()->addFieldToFilter('session_id', $sessionId);
        foreach ($sessionLoginCollection as $sessionLogin) {
            $sessionLogin->delete();
        }
        return true;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function changepassword()
    {
        $params = $this->getRequest()->getBodyParams();
        $staff = $params['staff'];
        $staffModel = Mage::getModel('webpos/user')->load(Mage::helper('webpos/permission')->getCurrentUser());
        $result = array();
        if (!$staffModel->getId()) {
            $result['error'] = '401';
            $result['message'] = __('There is no staff!');
            return \Zend_Json::encode($result);
        }
        $staffModel->setDisplayName($staff['username']);
        $oldPassword = $staffModel->getPassword();
        if ($staffModel->validatePassword($staff['old_password'])) {
            if ($staff['password']) {
                $staffModel->setNewPassword($staff['password']);
            }
        } else {
            $result['error'] = '1';
            $result['message'] = __('Old password is incorrect!');
            return \Zend_Json::encode($result);
        }
        try {
            $staffModel->save();
            $newPassword = $staffModel->getPassword();
            if ($newPassword != $oldPassword) {
                $sessionParam = $this->getRequest()->getParam('session');
                $userSession = Mage::getModel('webpos/user_webpossession')->getCollection()
                    ->addFieldToFilter('staff_id', array('eq' => $staffModel->getId()))
                    ->addFieldToFilter('session_id', array('neq' => $sessionParam));
                foreach ($userSession as $session) {
                    $session->delete();
                }
            }
        } catch (\Exception $e) {
            $result['error'] = '1';
            $result['message'] = $e->getMessage();
            return \Zend_Json::encode($result);
        }
        $result['error'] = '0';
        $result['message'] = __('Your account is saved successfully!');
        return \Zend_Json::encode($result);
    }


	
	    //<!--Edit by 28122017 Islam ELgarhy -->
    protected function getMyStaffList()
    {
        /*
       $staffId = Mage::helper('webpos/permission')->getCurrentUser();
       $staffCollection = Mage::getModel('webpos/user')->getCollection()->addFieldToFilter
        (array('head_user_id', 'user_id'), // columns
        array( // conditions
            array('eq' => $staffId),
            array('eq' => $staffId) // condition for field 2
           
        ))->load();
*/
        
        $staffId = Mage::helper('webpos/permission')->getCurrentUser();
        //$sql    = " select * , @pv from `webpos_user` where user_id = '" . $staffId ."' UNION select * from (select * from `webpos_user` order by head_user_id, user_id) u, (select @pv := '" . $staffId . "') hu where find_in_set(head_user_id, @pv) and length(@pv := concat(@pv, ',', user_id))";
        
        $sql = "select user_id , display_name from webpos_user where user_id = '" . $staffId ."' UNION select  p1.user_id , p1.display_name
        from        webpos_user p1
        left join   webpos_user p2 on p2.user_id = p1.head_user_id 
        left join   webpos_user p3 on p3.user_id = p2.head_user_id 
        left join   webpos_user p4 on p4.user_id = p3.head_user_id  
        left join   webpos_user p5 on p5.user_id = p4.head_user_id  
        left join   webpos_user p6 on p6.user_id = p5.head_user_id
        where       " . $staffId  . " in (p1.head_user_id, 
                           p2.head_user_id, 
                           p3.head_user_id, 
                           p4.head_user_id, 
                           p5.head_user_id, 
                           p6.head_user_id) 
        order       by 1";
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $staffCollection = $connection->fetchAll($sql);
        
        return $staffCollection;
        
        //            array('sog' => 'c'),

        //return Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*');
    }
    
    protected function getMySalesOfStaffList()
    {
        $requestData = $this->getRequest()->getBodyParams();
        $dateFrom= $requestData['fromDate'] . " " . "00:00:00";
        $dateTo= $requestData['toDate'] . " " . "23:59:59";
        $orderStatus= $requestData['orderStatus'];
        $staffId= $requestData['staffId'];
        $shippingMethod = $requestData['shippingMethod'];
        $shippingCondition = '';
        if($shippingMethod != "any")
        {
            $shippingCondition = 'AND (`sales/order`.`shipping_method` = "' . $shippingMethod . '")';
        }
        if($staffId == "-1")
        {
            $userIds=array();
            
            $myStaffId = Mage::helper('webpos/permission')->getCurrentUser();
          $sql = "select user_id , display_name from webpos_user where user_id = '" . $myStaffId ."' UNION select  p1.user_id , p1.display_name
        from        webpos_user p1
        left join   webpos_user p2 on p2.user_id = p1.head_user_id 
        left join   webpos_user p3 on p3.user_id = p2.head_user_id 
        left join   webpos_user p4 on p4.user_id = p3.head_user_id  
        left join   webpos_user p5 on p5.user_id = p4.head_user_id  
        left join   webpos_user p6 on p6.user_id = p5.head_user_id
        where       " . $myStaffId  . " in (p1.head_user_id, 
                           p2.head_user_id, 
                           p3.head_user_id, 
                           p4.head_user_id, 
                           p5.head_user_id, 
                           p6.head_user_id) 
        order       by 1";
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $staffCollection = $connection->fetchAll($sql);
        
            /*$mystaffId = Mage::helper('webpos/permission')->getCurrentUser();
            $collection = Mage::getModel('webpos/user')->getCollection()
            ->addFieldToFilter(array('head_user_id', 'user_id'), // columns
            array( // conditions
                array('eq' => $mystaffId),
                array('eq' => $mystaffId) // condition for field 2
           
             ))->load();
             */

            
            foreach ($staffCollection as $item){
                $userIds[] = $item['user_id'];
            }


        }
        
        if($orderStatus[0] == 'any')
        {
            if($staffId == "-1")
            {
                $sql =  Mage::getResourceModel('sales/order_collection')             
                ->addFieldToFilter('main_table.webpos_staff_id', array('in' => $userIds))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('gteq' =>$dateFrom))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('lteq' =>$dateTo))
                ->join('sales/order','`sales/order`.entity_id=main_table.entity_id',array('shipping_delivery_date', 'shipping_arrival_date','shipping_method','shipping_description'),null, 'left')
                ->join('sales/order_item','`sales/order_item`.order_id=main_table.entity_id',array('discount_cause', 'discount_by'),null, 'left')
                //->join('driver_delivery_information','`driver_delivery_information`.order_id=main_table.increment_id',array('note', 'reason'),null, 'left')
                ->getSelect()
                //->group('main_table.entity_id')
                ->__toString();
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                //return $sql;
                $sql = $sql . $shippingCondition . 'AND (((`sales/order`.`shipping_delivery_date` IS NOT NULL) AND (`sales/order`.`shipping_delivery_date` >= "' . $dateFrom . '") AND (`sales/order`.`shipping_delivery_date` <= "' . $dateTo . '")) OR ((`sales/order`.`shipping_delivery_date` IS NULL) AND (`main_table`.`created_at` >= "'. $dateFrom .'" ) AND (`main_table`.`created_at` <= "' .$dateTo . '") )) GROUP BY `main_table`.`entity_id`';
                Mage::log($sql ,null,'0.log');
                $salesList= $connection->fetchAll($sql);
            }
            
            else
            {

                $sql =  Mage::getResourceModel('sales/order_collection')             
                ->addFieldToFilter('main_table.webpos_staff_id', $staffId)
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('gteq' =>$dateFrom))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('lteq' =>$dateTo))
                ->join('sales/order','`sales/order`.entity_id=main_table.entity_id',array('shipping_delivery_date', 'shipping_arrival_date','shipping_method','shipping_description'),null, 'left')
                ->join('sales/order_item','`sales/order_item`.order_id=main_table.entity_id',array('discount_cause', 'discount_by'),null, 'left')
                ->getSelect()
                //->group('main_table.entity_id')
                ->__toString();
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = $sql . $shippingCondition . 'AND (((`sales/order`.`shipping_delivery_date` IS NOT NULL) AND (`sales/order`.`shipping_delivery_date` >= "' . $dateFrom . '") AND (`sales/order`.`shipping_delivery_date` <= "' . $dateTo . '")) OR ((`sales/order`.`shipping_delivery_date` IS NULL) AND (`main_table`.`created_at` >= "'. $dateFrom .'" ) AND (`main_table`.`created_at` <= "' .$dateTo . '") )) GROUP BY `main_table`.`entity_id`';
                $salesList= $connection->fetchAll($sql);

            }
            
        }
        else
        {
            // Islam Kuywait 2018_2

            $orderStatusArr = array();
            foreach ($orderStatus as $status){
                $orderStatusArr[] = $status;
            }
           if($staffId == "-1")
            {
                 $sql =  Mage::getResourceModel('sales/order_collection')             
                ->addFieldToFilter('main_table.webpos_staff_id', array('in' => $userIds))
                ->addFieldToFilter('main_table.order_status', array('in' => $orderStatusArr))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('gteq' =>$dateFrom))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('lteq' =>$dateTo))
                ->join('sales/order','`sales/order`.entity_id=main_table.entity_id',array('shipping_delivery_date', 'shipping_arrival_date','shipping_method','shipping_description'),null, 'left')
                ->join('sales/order_item','`sales/order_item`.order_id=main_table.entity_id',array('discount_cause', 'discount_by'),null, 'left')
                ->getSelect()
                //->group('main_table.entity_id')
                ->__toString();
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = $sql . $shippingCondition . 'AND (((`sales/order`.`shipping_delivery_date` IS NOT NULL) AND (`sales/order`.`shipping_delivery_date` >= "' . $dateFrom . '") AND (`sales/order`.`shipping_delivery_date` <= "' . $dateTo . '")) OR ((`sales/order`.`shipping_delivery_date` IS NULL) AND (`main_table`.`created_at` >= "'. $dateFrom .'" ) AND (`main_table`.`created_at` <= "' .$dateTo . '") )) GROUP BY `main_table`.`entity_id`';
                $salesList= $connection->fetchAll($sql);


            }
            else{
                
                $sql =  Mage::getResourceModel('sales/order_collection')             
                ->addFieldToFilter('main_table.webpos_staff_id',$staffId)
                ->addFieldToFilter('main_table.order_status', array('in' => $orderStatusArr))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('gteq' =>$dateFrom))
                //->addFieldToFilter('sales/order.shipping_delivery_date', array('lteq' =>$dateTo))
                ->join('sales/order','`sales/order`.entity_id=main_table.entity_id',array('shipping_delivery_date', 'shipping_arrival_date','shipping_method','shipping_description'),null, 'left')
                ->join('sales/order_item','`sales/order_item`.order_id=main_table.entity_id',array('discount_cause', 'discount_by'),null, 'left')
                ->getSelect()
                //->group('main_table.entity_id')
                ->__toString();
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = $sql . $shippingCondition . 'AND (((`sales/order`.`shipping_delivery_date` IS NOT NULL) AND (`sales/order`.`shipping_delivery_date` >= "' . $dateFrom . '") AND (`sales/order`.`shipping_delivery_date` <= "' . $dateTo . '")) OR ((`sales/order`.`shipping_delivery_date` IS NULL) AND (`main_table`.`created_at` >= "'. $dateFrom .'" ) AND (`main_table`.`created_at` <= "' .$dateTo . '") )) GROUP BY `main_table`.`entity_id`';
                $salesList= $connection->fetchAll($sql);
            }
            
        }
  
       //->addAttributeToFilter('created_at', array('from' => $dateFrom,'to' => $dateTo,'date' => true,))
       //return $userIds;

       foreach ($salesList as $key => $sorder) {
            if ($sorder['order_status']=='returned')
            {
                $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');					
                $select1 = $connectionRead->select()
                                    ->from('driver_delivery_information', array('reason'))
                                    ->where('order_id=?',$sorder['increment_id']);
                
                $reason_text = $connectionRead->fetchRow($select1);
                if($reason_text == false)
                    $salesList[$key]['reason_text'] = '';
                else
                {
                    $salesList[$key]['reason_text']= $reason_text['reason'];
                    if($reason_text['reason'] == '1')
                        $salesList[$key]['reason_text']= 'Customer not answering bell and phone';
                    if($reason_text['reason'] == '2')
                        $salesList[$key]['reason_text']= 'Wrong address or phone number'; 
                    if($reason_text['reason'] == '3')
                        $salesList[$key]['reason_text']= 'Order late form company'; 
                    if($reason_text['reason'] == '4')
                        $salesList[$key]['reason_text']= 'Wrong Order'; 
                }

            }
            else {
                $salesList[$key]['reason_text']= '';
            }
        }

       return $salesList;
    }
    

 //<!--Edit by 28122017 Islam ELgarhy -->
    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Create */
            case self::ACTION_TYPE_ENTITY_LOGIN . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('login');
                $retrievedData = $this->login();
                $this->_render($retrievedData);
                break;
            case self::ACTION_TYPE_ENTITY_ACTIVEDISCOUNT . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('activediscount');
                $retrievedData = $this->activediscount();
                $this->_render($retrievedData);
                break;
            case self::ACTION_TYPE_ENTITY_LOGOUT . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('logout');
                $retrievedData = $this->logout();
                $this->_render($retrievedData);
                break;
            case self::ACTION_TYPE_ENTITY_CHANGEPASSWORD . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('changepassword');
                $retrievedData = $this->changepassword();
                $this->_render($retrievedData);
                break;
				 //<!--Edit by 28122017 Islam ELgarhy -->
			 case self::OPERATION_GET_STAFF_LIST . self::OPERATION_CREATE:
				$this->_errorIfMethodNotExist('getMyStaffList');
                $retrievedData = $this->getMyStaffList();
                $this->_render($retrievedData);
                break;
            case self::OPERATION_GET_SALESSTAFF_LIST . self::OPERATION_CREATE:
				$this->_errorIfMethodNotExist('getMySalesOfStaffList');
                $retrievedData = $this->getMySalesOfStaffList();
                $this->_render($retrievedData);
                break;
				 //<!--Edit by 28122017 Islam ELgarhy -->
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
				
				 
        }
    }

}
