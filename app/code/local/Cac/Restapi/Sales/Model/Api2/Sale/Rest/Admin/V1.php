<?php

class Cac_Restapi_Sales_Model_Api2_Sale_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_ORDERS_STATUS = 'status';
    const OPERATION_GET_ORDERS_PERIOD = 'period';
    const OPERATION_GET_ORDER_LIST = 'list';
    const OPERATION_GET_ORDER_LIST_24H = 'list24h';
    const OPERATION_GET_ORDERS_KITCHEN = 'kitchen';
    const OPERATION_GET_ORDERS_KITCHEN_DEPARTMENTS = 'kitchen_departments';
    const OPERATION_GET_ORDERS_BY_PAYMENT_METHOD = 'by_payment_method';


    //customized _retrieve method
    public function getOrdersPeriod()
    {
        // route:
        // /cac/sale/period/:year/:month/:day

        $year = $this->getRequest()->getParam('year');
        $month = $this->getRequest()->getParam('month');
        $day = $this->getRequest()->getParam('day');

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "SELECT entity_id,increment_id as order_id,webpos_staff_name,order_status,total_qty_ordered,subtotal_invoiced,concat(customer_firstname,\" \",customer_lastname,\" \") as customer_name, customer_id FROM sales_flat_order where DATE_FORMAT(shipping_delivery_date, \"%Y-%m-%d\")=\"$year-$month-$day\" order by created_at ";
        $results["items"] = $readConnection->fetchAll($query);
        $results["count"]= count($results["items"]);


        return $results;


    }

    public function getOrdersStatus()
    {
        // route:
        // /cac/sale/status/:year/:month/:day

        $year = $this->getRequest()->getParam('year');
        $month = $this->getRequest()->getParam('month');
        $day = $this->getRequest()->getParam('day');


        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');

        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');


        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();

        $status_agr="";

        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];

            if (strlen($status_agr)>0)
                $status_agr=$status_agr . ",";
//            $status_agr=$status_agr . "(select count(*) from sales_flat_order as s2 where order_status='$status[value]' and date_format(s2.shipping_delivery_date,\"%Y-%m-%d\")=period) as $status[value]";
            $status_agr=$status_agr . "sum(CASE WHEN order_status='$status[value]' THEN 1 ELSE 0 END) as $status[value]";
        }


        $query = "SELECT date_format(shipping_delivery_date,\"%Y-%m-%d\") as period, (count(*)-sum(CASE WHEN order_status='canceled' THEN 1 ELSE 0 END)) as total,$status_agr FROM sales_flat_order where YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month group by period";


        /**
         * Execute the query and store the results in $results
         */
        try {
            $results = $readConnection->fetchAll($query);
        } catch (Exception $err) {
            error_log("EXCEPTION : " . $err->getMessage());
        }


        return $results;

    }

    function getDrivers()
    {
        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(array('o' => 'admin_role'), "o.user_id = main_table.user_id", array('*'));
        $collection->getSelect()->order('username', 'asc');
        $collection->addFieldToFilter('parent_id', 5);
        $drivers = array();
        foreach ($collection as $key => $value) {
            $drivers[$value->getUserId()] = $value->getUsername();
        }
        return $drivers;
    }


    public function getOrders()
    {
//        $year = $this->getRequest()->getParam('year');
//        $month = $this->getRequest()->getParam('month');
//        $day = $this->getRequest()->getParam('day');
        $filters =  explode(",",trim($this->getRequest()->getParam('filter')));
        $sortby = trim(strtolower($this->getRequest()->getParam('sortby')));
        $sort = $this->getRequest()->getParam('sort');

        if (!isset($sort) || strlen($sort)==0)
        {
            $sortby="";
        }
        else
        {
            if ($sortby=="entityid")
                $sortby="entity_id";
            else if ($sortby=="deliverydate" || $sortby=="orderdate")
                $sortby="shipping_delivery_date";
            else if ($sortby=="orderno")
                $sortby="increment_id";
            else if ($sortby=="orderstatus")
                $sortby="order_status";
            else if ($sortby=="customerid")
                $sortby="customer_id";
            else if ($sortby=="customername")
                $sortby="firstname+lastname";
        }

        if (strlen($sortby)==0)
        {
            $sortby="created_at";
            $sort="desc";
        }

        $page=0;
        $pagesize=32;

        if ($this->getRequest()->getParam('page')!=null)
            $page = $this->getRequest()->getParam('page');
        if ($this->getRequest()->getParam('limit')!=null)
            $pagesize = $this->getRequest()->getParam('limit');

        // route:
        // /cac/sale/list/:year/:month/:day
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('*')
            ->setOrder($sortby, $sort)
            ->setPageSize($pagesize)
            ->setCurPage($page);

        if (count($filters)>0 && strlen($filters[0])>0) {
//            $orders->getSelect()->join('sales_flat_order_item', 'main_table.entity_id = sales_flat_order_item.order_id',array('name'));
//            $orders->getSelect()->where("concat(main_table.customer_lastname,main_table.customer_firstname,sales_flat_order_item.name) like '%$filter%'");
            $sql_clause = "";


            $filter_clause="";
            foreach ($filters as $filter)
            {
                if (strlen($filter_clause)>0)
                    $filter_clause = $filter_clause . " and (";
                else
                    $filter_clause="(";
                $filter_clause = $filter_clause . "((select count(*) from sales_flat_order_item where sales_flat_order_item.order_id=main_table.entity_id and ";
                $filter_clause = $filter_clause . " (concat(COALESCE(main_table.order_status,''),' ',COALESCE(main_table.increment_id,''),' ',COALESCE(main_table.shipping_delivery_date,''),' ', COALESCE(main_table.customer_lastname,''),' ',COALESCE(main_table.customer_firstname,''),' ',COALESCE(sales_flat_order_item.name,''),' ',COALESCE(webpos_staff_name,'')) like '%$filter%')))>0 " .
                    " or ".
                                        " (select count(*) from admin_user where admin_user.user_id=main_table.driver_id and admin_user.username like '%$filter%' >0) ".
                    " or ".

                    //address
                "(select count(*) from sales_flat_order_address where sales_flat_order_address.parent_id=main_table.entity_id and ".
                " concat(COALESCE(region,''),COALESCE(street,''),COALESCE(city,''),COALESCE(telephone,''),COALESCE(email,'')) like '%$filter%')>0 )" ;

//                $filter_clause = $filter_clause . ")";
            }
//            if (strlen($filter_clause)>1)
//                $filter_clause=$filter_clause . ")";
            $sql_clause = $sql_clause . $filter_clause;
            $orders->getSelect()->where($sql_clause);
        }

        $driver_list = $this->getDrivers();

        $all_orders = [];
        $counter = 0;

        try {
            foreach ($orders as $order) {
                $this_order = [];
                $items = [];

                foreach ($order->getItemsCollection() as $item) {
                    if ($order->increment_id)
                    $simple_item["ProductName"] = $item->getProduct()->getName();
                    $simple_item["Message"] = $item->getData("text_custom_options_value");
                    $items[] = $simple_item;
                }
                $this_order["OrderNo"] = $order->getRealOrderId();
                $this_order["EntityID"] = $order->getEntityId();
                $this_order["OrderDate"] = $order->getUpdatedAt();
                $this_order["OrderStatus"] = $order->getData("order_status");

                $this_order["CustomerID"] = $order->getCustomerId();
                $this_order["CustomerName"] = $order->getCustomerName();

                $delivery = [];

                if ($order->getShippingAddress()) {
                    $_shippingAdd = $order->getShippingAddress();
                    $block= $_shippingAdd->getCompany();
                    $street=$_shippingAdd->getStreet()[0];
                    $building=$_shippingAdd->getCity();
                    $country = Mage::getModel('directory/country')->loadByCode($_shippingAdd->getCountryId())->getName();
                    $area=$_shippingAdd->getRegion();

                    $delivery["Address"] = "Block:$block, Street:$street, Building:$building, Area:$area, Country:$country";
                }
                $delivery["DeliveryDate"] = $order->getData('shipping_delivery_date');
                $delivery["ArrivalDate"] = $order->getData('shipping_arrival_date');
                $delivery["ShippingDescription"] = $order->getData('shipping_description');
                $delivery["DriverID"] = $order->getDriverId();
                $delivery["DriverName"] = $driver_list[$order->getDriverId()];

                $this_order["Delivery"] = $delivery;

                $this_order["OrderItems"] = $items;
                $all_orders[] = $this_order;
                $counter++;
                if ($counter > 100)
                    break;
            }
            $results["items"] = $all_orders;
            $results["count"] = $orders->getSize();
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSalesByPaymentMethod()
    {
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');
        $paymentMethod = $this->getRequest()->getParam('method');
        if (!$paymentMethod) {
            throw new Exception('No payment method is specified');
        }

        $whereFrom = $whereTo = '';
        if ($from) {
            $fromDate = date('Y-m-d H:i:s', $from);
            $whereFrom = $fromDate ? " AND sfo.created_at >= '{$fromDate}'" : "";
        }
        if ($to) {
            $toDate = date('Y-m-d H:i:s', $to);
            $whereTo = $toDate ? " AND sfo.created_at < '{$toDate}'" : "";
        }



        $page=0;
        $pageSize=32;

        if ($this->getRequest()->getParam('page')!=null)
            $page = $this->getRequest()->getParam('page');
        if ($this->getRequest()->getParam('limit')!=null)
            $pageSize = $this->getRequest()->getParam('limit');

        $offset = $page * $pageSize;

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = "select
                  sfo.entity_id as order_number,
                  sfo.subtotal,
                  sfo.subtotal_invoiced,
                  sfo.total_paid,
                  op.method,
                  op.method_title,
                  sfo.store_name,
                  sfo.shipping_description,
                  sfo.order_status,
                  sfo.shipping_delivery_date,
                  sfo.webpos_delivery_date,
                  sfo.created_at
                from sales_flat_order sfo
                  left join webpos_order_payment op on sfo.entity_id = op.order_id
                where op.method = '{$paymentMethod}' {$whereFrom} {$whereTo}
                limit {$offset}, {$pageSize}
        ";

        $salesList = $readConnection->fetchAll($query);


        $query = "select
                      sfo.store_name,
                      count(*)            as total_orders,
                      sum(sfo.total_paid) as total_sales
                    from sales_flat_order sfo
                      left join webpos_order_payment op on sfo.entity_id = op.order_id
                    where op.method = '{$paymentMethod}' {$whereFrom} {$whereTo}
                    group by sfo.store_name
        ";

        $storeData = $readConnection->fetchAll($query);

        $totalOrders = array_sum(array_column($storeData,'total_orders'));
        $totalSales = array_sum(array_column($storeData,'total_sales'));


        $result['stats'][] =
            [
                'store' => 'total',
                'total_orders' => (float)$totalOrders,
                'total_sales' => (float)number_format($totalSales, 2, '.', ''),
            ];


        foreach ($storeData as $store) {
            $nameParts = explode(PHP_EOL, $store['store_name']);
            $storeName = end($nameParts);
            $result['stats'][] = [
                'store' => $storeName,
                'total_orders' => (float)$store['total_orders'],
                'total_sales' => (float)number_format($store['total_sales'], 2, '.', ''),
            ];
        }
        $result['orders'] = $salesList;

        return $result;


    }

    public function getOrders24h()
    {
        $year = $this->getRequest()->getParam('year');
        $month = $this->getRequest()->getParam('month');
        $day = $this->getRequest()->getParam('day');

        // first get stores
        $stores_q="";
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
            if (strlen($stores_q)>0)
                $stores_q=$stores_q . ",";
            $stores_q = $stores_q . "((SELECT count(*) FROM sales_flat_order where store_id=$_storeId and YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day  and order_status!='canceled')) as '$_storeName'";
        }

        $total_query="SELECT count(*) as 'Total Of Day', $stores_q FROM sales_flat_order where YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day and order_status!='canceled'";

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($total_query)[0];

        $result["totals"]=$results;

        $hour_query="SELECT  HOUR(shipping_delivery_date) as thehour,count(*) as count_in_hour FROM sales_flat_order where YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day group by thehour";
        $hour_results = $readConnection->fetchAll($hour_query);
        $hourly_results=[];


        $previous_hour=-1;

        foreach ($hour_results as $hour_result)
        {
            $h=$hour_result['thehour'];
            if ($h>$previous_hour)
            {
                for ($x = $previous_hour+1; $x < $h; $x++) {
                    $hour_item['hour']=$x;
                    $hour_item['label']=sprintf("%02d", $x) . ":00 to " . sprintf("%02d", $x+1).":00";
                    $hour_item['count']=0;
                    $hourly_results[] = $hour_item;
                }
            }
            $previous_hour=$h;
            $ih=(int)$h;
            $hour_item['hour']=$ih;
            $hour_item['label']=sprintf("%02d", $ih) . ":00 to " . sprintf("%02d", $ih+1).":00";;
            $hour_item['count']=(int)$hour_result['count_in_hour'];
            $hourly_results[] = $hour_item;
        }
        if ($previous_hour<24)
        {
            for ($x = $previous_hour+1; $x <=23 ; $x++) {
                $hour_item['hour']=$x;
                $hour_item['label']=sprintf("%02d", $x) . ":00 to " . sprintf("%02d", $x+1).":00";
                $hour_item['count']=0;
                $hourly_results[] = $hour_item;
            }
        }


        $result['hourly']=$hourly_results;


        return $result;
    }

    public function getKitchenOrders()
    {
        // route:
        // /cac/sale/kitchen
        return 'hello';
    }

    public function getKitchenDepartments()
    {
        // route:
        // /cac/sale/kitchen_departments
        return 'hello';
    }

    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_ORDERS_PERIOD:
                $result = $this->getOrdersPeriod();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDERS_STATUS:
                $result = $this->getOrdersStatus();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_LIST:
                $result = $this->getOrders();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDER_LIST_24H:
                $result = $this->getOrders24h();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDERS_KITCHEN:
                $result = $this->getKitchenOrders();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDERS_KITCHEN_DEPARTMENTS:
                $result = $this->getKitchenDepartments();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDERS_BY_PAYMENT_METHOD:
                try {
                    $result = $this->getSalesByPaymentMethod();
                    $status = Mage_Api2_Model_Server::HTTP_OK;
                } catch (Exception $exception) {
                    $result = [
                      'error' => 'Error getting list of orders',
                      'message' => $exception->getMessage()
                    ];
                    $status = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
                }
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode($status);
                break;

        }
    }

}