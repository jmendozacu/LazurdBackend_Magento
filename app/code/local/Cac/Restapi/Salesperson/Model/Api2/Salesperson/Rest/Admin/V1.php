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
        $user0 = Mage::getModel('webpos/user')->load($salesperson_id);

        $user['user_id']=$user0->getData('user_id');
        $user['username']=$user0->getData('username');
        $user['display_name']=$user0->getData('display_name');
        $user['email']=$user0->getData('email');
        $user['location_id']=$user0->getData('location_id');
        $user['store_id']=$user0->getData('store_id');

        return $user;
    }

    /**
     * /cac/salesperson/:userid/:from/:to
     */
    public function getSalespersonOrderHistory(){
        $salesperson_id = $this->getRequest()->getParam('userid');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');

        list($year_s,$month_s,$day_s)=explode("-",$from);
        list($year_e,$month_e,$day_e)=explode("-",$to);

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "SELECT * from cac_staff_sales_history where created_at > '$year_s-$month_s-$day_s 0:0' and created_at < '$year_e-$month_e-$day_e 23:59'";
        $results["items"] = $readConnection->fetchAll($query);


        return $results;

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