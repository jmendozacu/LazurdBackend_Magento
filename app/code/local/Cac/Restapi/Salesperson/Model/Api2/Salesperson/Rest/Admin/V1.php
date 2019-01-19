<?php

class Cac_Restapi_Salesperson_Model_Api2_Salesperson_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_SALESPERSON_LIST = "salesperson_list";
    const OPERATION_GET_SALESPERSON_STATUS = "salesperson_status";
    const OPERATION_GET_SALESPERSON_ORDERS_HISTORY = "salesperson_orders";



    /**
     * /cac/salesperson/list
     */
    public function getSalespersonList(){

       $webPos_users_collection = Mage::getModel('webpos/user')->getCollection();
       $webPos_users_collection = $webPos_users_collection->getData('items');
    /*   $webPos_users_collection = json_encode($webPos_users_collection);
       $webpos_array =json_decode($webPos_users_collection);

       $webpos_users= [];

        foreach ($webpos_array as $item) {
            $user['user_id'] = $item->user_id;
            $user['display_name'] = $item->display_name;
            $webpos_users[] = $user;
       }

        $result = json_encode($webpos_users, JSON_UNESCAPED_SLASHES);
    */
        return $webPos_users_collection;

    }

    /**
     * /cac/salesperson/status/:userid/:from/:to
     */
    public function getSalespersonStatus()
    {
        $salesperson_id = $this->getRequest()->getParam('userid');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');





        return $salesperson_id;
    }

    /**
     * /cac/salesperson/orders/:userid/:from/:to
     */
    public function getSalespersonOrders(){
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
            case self::OPERATION_GET_SALESPERSON_STATUS:
                $result = $this->getSalespersonStatus();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_SALESPERSON_ORDERS_HISTORY:
                $result = $this->getSalespersonOrders();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

}