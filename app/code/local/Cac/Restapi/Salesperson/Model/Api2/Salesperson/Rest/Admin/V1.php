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

        list($year_s,$month_s,$day_s)=explode("-",$from);
        list($year_e,$month_e,$day_e)=explode("-",$to);

        // first get stores
        $allStores = Mage::app()->getStores();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $date_range="created_at > '$year_s-$month_s-$day_s 0:0' and created_at < '$year_e-$month_e-$day_e 23:59'";
        $date_range_delivery="shipping_delivery_date > '$year_s-$month_s-$day_s 0:0' and shipping_delivery_date < '$year_e-$month_e-$day_e 23:59'";
       // second get webpos users total_sales and count
        $query="select sum(subtotal_invoiced) as daily_sales,count(*) as total_sales_by_person".
            " from sales_flat_order_journal ".
            " where $date_range_delivery and order_status != 'canceled' and webpos_staff_id=$salesperson_id";
        $results = $readConnection->fetchAll($query)[0];
        $i=0;
        $stores=[];
        // finally get total sales and count of each store
        foreach ($allStores as $_eachStoreId => $val)
        {
            $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $_storeId = Mage::app()->getStore($_eachStoreId)->getId();

            $stores_q = "SELECT count(*) as total_number_of_orders,sum(subtotal_invoiced) as total_sales".
                " FROM sales_flat_order ".
                " where store_id=$_storeId and $date_range_delivery and order_status!='canceled'";
            $stores[$i] = $readConnection->fetchAll($stores_q)[0];
            $stores[$i]["store_name"] = $_storeName;
            $stores[$i]["total_number_of_orders"] = (float)$stores[$i]["total_number_of_orders"];
            $stores[$i]["total_sales"] = (float)$stores[$i]["total_sales"];
            $i++;
        }
        $results["daily_sales"]=(float)$results["daily_sales"];
        $results["total_sales_by_person"]=(float)$results["total_sales_by_person"];
        $results["stores"]=$stores;
        return $results;
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
        $query = "SELECT * from sales_flat_order_journal where webpos_staff_id=$salesperson_id and shipping_delivery_date > '$year_s-$month_s-$day_s 0:0' and shipping_delivery_date < '$year_e-$month_e-$day_e 23:59'";
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