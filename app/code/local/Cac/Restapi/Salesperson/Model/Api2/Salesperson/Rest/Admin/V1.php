<?php

class Cac_Restapi_Salesperson_Model_Api2_Salesperson_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_SALESPERSON_LIST = "salesperson_list";
    const OPERATION_GET_SALESPERSON_PROFILE = "salesperson_profile";
    const OPERATION_GET_SALESPERSON_ORDERS_HISTORY = "salesperson_orders_history";



    /**
     * /cac/salesperson/
     */
    public function getSalespersonList(){


        return "List of Salesperson";

    }

    /**
     * /cac/salesperson/:userid
     */
    public function getSalespersonProfile()
    {
        $salesperson_id = $this->getRequest()->getParam('userid');

        $apiUser=$this->getApiUser();
        $user_object=Mage::getModel('admin/user')->load($salesperson_id);
        $role_data = $user_object->getRole()->getData();

        $user['user_id']=$apiUser->getUserId();
        $user['role_id']=$role_data['role_id'];
        $user['role_name']=$role_data['role_name'];
        $profile=$user_object->getData();
        $user['firstname']=$profile['firstname'];
        $user['lastname']=$profile['lastname'];
        return $user;
    }

    /**
     * /cac/salesperson/:userid/:from/:to
     */
    public function getSalespersonOrderHistory(){
        $salesperson_id = $this->getRequest()->getParam('userid');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');

        return $salesperson_id. " : " .$from. " - " .$to;

    }

    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_SALESPERSON_LIST:
                $result = $this->getSalespersonList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_SALESPERSON_PROFILE:
                $result = $this->getSalespersonProfile();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_SALESPERSON_ORDERS_HISTORY:
                $result = $this->getSalespersonOrderHistory();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

}