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
    const OPERATION_GET_PAYMENT_METHODS = 'payment_methods';
    const OPERATION_GET_ORDER_DETAIL = 'order_detail';


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
        $results["count"] = count($results["items"]);


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
        $customstatus = array();

        $status_agr = "";

        foreach ($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];

            if (strlen($status_agr) > 0)
                $status_agr = $status_agr . ",";
//            $status_agr=$status_agr . "(select count(*) from sales_flat_order as s2 where order_status='$status[value]' and date_format(s2.shipping_delivery_date,\"%Y-%m-%d\")=period) as $status[value]";
            $status_agr = $status_agr . "sum(CASE WHEN order_status='$status[value]' THEN 1 ELSE 0 END) as $status[value]";
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
        $filters = explode(",", trim($this->getRequest()->getParam('filter')));
        $sortby = trim(strtolower($this->getRequest()->getParam('sortby')));
        $sort = $this->getRequest()->getParam('sort');

        if (!isset($sort) || strlen($sort) == 0) {
            $sortby = "";
        } else {
            if ($sortby == "entityid")
                $sortby = "entity_id";
            else if ($sortby == "deliverydate" || $sortby == "orderdate")
                $sortby = "shipping_delivery_date";
            else if ($sortby == "orderno")
                $sortby = "increment_id";
            else if ($sortby == "orderstatus")
                $sortby = "order_status";
            else if ($sortby == "customerid")
                $sortby = "customer_id";
            else if ($sortby == "customername")
                $sortby = "firstname+lastname";
        }

        if (strlen($sortby) == 0) {
            $sortby = "created_at";
            $sort = "desc";
        }

        $page = 0;
        $pagesize = 32;

        if ($this->getRequest()->getParam('page') != null)
            $page = $this->getRequest()->getParam('page');
        if ($this->getRequest()->getParam('limit') != null)
            $pagesize = $this->getRequest()->getParam('limit');

        // route:
        // /cac/sale/list/:year/:month/:day
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('*')
            ->setOrder($sortby, $sort)
            ->setPageSize($pagesize)
            ->setCurPage($page);

        if (count($filters) > 0 && strlen($filters[0]) > 0) {
//            $orders->getSelect()->join('sales_flat_order_item', 'main_table.entity_id = sales_flat_order_item.order_id',array('name'));
//            $orders->getSelect()->where("concat(main_table.customer_lastname,main_table.customer_firstname,sales_flat_order_item.name) like '%$filter%'");
            $sql_clause = "";


            $filter_clause = "";
            foreach ($filters as $filter) {
                if (strlen($filter_clause) > 0)
                    $filter_clause = $filter_clause . " and (";
                else
                    $filter_clause = "(";
                $filter_clause = $filter_clause . "((select count(*) from sales_flat_order_item where sales_flat_order_item.order_id=main_table.entity_id and ";
                $filter_clause = $filter_clause . " (concat(COALESCE(main_table.order_status,''),' ',COALESCE(main_table.increment_id,''),' ',COALESCE(main_table.shipping_delivery_date,''),' ', COALESCE(main_table.customer_lastname,''),' ',COALESCE(main_table.customer_firstname,''),' ',COALESCE(sales_flat_order_item.name,''),' ',COALESCE(webpos_staff_name,'')) like '%$filter%')))>0 " .
                    " or " .
                    " (select count(*) from admin_user where admin_user.user_id=main_table.driver_id and admin_user.username like '%$filter%' >0) " .
                    " or " .

                    //address
                    "(select count(*) from sales_flat_order_address where sales_flat_order_address.parent_id=main_table.entity_id and " .
                    " concat(COALESCE(region,''),COALESCE(street,''),COALESCE(city,''),COALESCE(telephone,''),COALESCE(email,'')) like '%$filter%')>0 )";

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
                    $block = $_shippingAdd->getCompany();
                    $street = $_shippingAdd->getStreet()[0];
                    $building = $_shippingAdd->getCity();
                    $country = Mage::getModel('directory/country')->loadByCode($_shippingAdd->getCountryId())->getName();
                    $area = $_shippingAdd->getRegion();

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

    public function getPaymentMethods()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');


        $query = "
                select method as code, method_title as title from webpos_order_payment
                group by method, method_title
                order by title
                ";

        $paymentMethods = $readConnection->fetchAll($query);

        return $paymentMethods;

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

        $wherePaymentMethod = $whereFrom = $whereTo = '';

        if ($paymentMethod && $paymentMethod !== 'all') {
            $wherePaymentMethod = " AND op.method = '{$paymentMethod}'";
        }

        if ($from) {
            if (strlen($from) > 10) {
                $from = (int)($from / 1000);
            }
            $fromDate = date('Y-m-d', $from);
            $whereFrom = $fromDate ? " AND DATE(sfo.shipping_delivery_date) >= '{$fromDate}'" : "";
        }
        if ($to) {
            if (strlen($to) > 10) {
                $to = (int)($to / 1000);
            }
            $toDate = date('Y-m-d', $to);
            $whereTo = $toDate ? " AND DATE(sfo.shipping_delivery_date) <= '{$toDate}'" : "";
        }

        $paginate = '';
        $pageSize = $this->getRequest()->getParam('limit') ?? null;
        $page = $this->getRequest()->getParam('page') ?? ($pageSize ? 1 : null);
        if ($page && $pageSize) {
            $offset = $page * $pageSize;
            $paginate = "limit {$offset}, {$pageSize}";
        }

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = "
            select
              sfo.entity_id,
              sfo.increment_id,
              sfo.subtotal,
              sfo.total_paid,
              sfo.total_due,
              op.method,
              op.method_title,
              cs.name as store_name,
              sfo.shipping_description,
              sfo.order_status,
              sfo.shipping_delivery_date,
              sfo.shipping_arrival_date
            from sales_flat_order sfo
              left join webpos_order_payment op on sfo.entity_id = op.order_id
              left join core_store cs on sfo.store_id = cs.store_id
            where `order_status` <> 'canceled' {$wherePaymentMethod} {$whereFrom} {$whereTo}
            {$paginate}
        ";

        $salesList = $readConnection->fetchAll($query);

        $query = "
            select count(*) as total
            from sales_flat_order sfo
              left join webpos_order_payment op on sfo.entity_id = op.order_id
              left join core_store cs on sfo.store_id = cs.store_id
            where `status` <> 'canceled' {$wherePaymentMethod} {$whereFrom} {$whereTo}
        ";

        $totalCount = $readConnection->fetchOne($query);

        $query = "
            select
              cs.name as store_name,
              count(*)            as total_orders,
              sum(sfo.total_paid) as total_sales,
              sum(sfo.total_due) as total_due
            from sales_flat_order sfo
              left join webpos_order_payment op on sfo.entity_id = op.order_id
              left join core_store cs on sfo.store_id = cs.store_id
            where `order_status` <> 'canceled' {$wherePaymentMethod} {$whereFrom} {$whereTo}
            group by store_name
        ";

        $storeData = $readConnection->fetchAll($query);

        $totalOrders = array_sum(array_column($storeData, 'total_orders'));
        $totalSales = array_sum(array_column($storeData, 'total_sales'));
        $totalDue = array_sum(array_column($storeData, 'total_due'));


        $result['stats'][] =
            [
                'store' => 'total',
                'total_orders' => (float)$totalOrders,
                'total_sales' => (float)number_format($totalSales, 2, '.', ''),
                'total_due' => (float)number_format($totalDue, 2, '.', ''),
            ];


        foreach ($storeData as $store) {
            $result['stats'][] = [
                'store' => $store['store_name'],
                'total_orders' => (float)$store['total_orders'],
                'total_sales' => (float)number_format($store['total_sales'], 2, '.', ''),
                'total_due' => (float)number_format($store['total_due'], 2, '.', ''),
            ];
        }
        $result['orders'] = $salesList;

        $result['_meta'] = [
            "page" => $page,
            "per_page" => $pageSize,
            "total_count" => $totalCount,
            "page_count" => ceil($totalCount / $pageSize),
        ];

        return $result;


    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function getOrderDetail()
    {
        $helper = Mage::helper('webpos/order');

        $orderId = $this->getRequest()->getParam('entity_id');
        if (!$orderId) {
            throw new Exception('Order ID (entity_id) is not specified.');
        }
        $order = Mage::getModel('sales/order')->load($orderId);
        // Get nesscessary information of order
        $i = 0;
        $orderedItems = $order->getAllVisibleItems();
        $orderedProductIds = array();
        foreach ($orderedItems as $item) {
            $orderedProductIds[$i]['item_id'] = $item->getData('item_id');
            $orderedProductIds[$i]['name'] = $item->getData('name');
            $orderedProductIds[$i]['created_at'] = $item->getData('created_at');
            $orderedProductIds[$i]['amount_refunded'] = (float)$item->getData('amount_refunded');
            $orderedProductIds[$i]['base_amount_refunded'] = (float)$item->getData('base_amount_refunded');
            $orderedProductIds[$i]['base_discount_amount'] = (float)$item->getData('base_discount_amount');
            $orderedProductIds[$i]['base_gift_voucher_discount'] = (float)$item->getData('base_gift_voucher_discount');
            $orderedProductIds[$i]['gift_voucher_discount'] = (float)$item->getData('gift_voucher_discount');
            $orderedProductIds[$i]['discount_amount'] = (float)$item->getData('discount_amount');
            $orderedProductIds[$i]['base_discount_invoiced'] = (float)$item->getData('base_discount_invoiced');
            $orderedProductIds[$i]['base_price'] = (float)$item->getData('base_price');
            $orderedProductIds[$i]['base_price_incl_tax'] = (float)$item->getData('base_price_incl_tax');
            $orderedProductIds[$i]['base_row_invoiced'] = (float)$item->getData('base_row_invoiced');
            $orderedProductIds[$i]['base_row_total'] = (float)$item->getData('base_row_total');
            $orderedProductIds[$i]['base_row_total_incl_tax'] = (float)$item->getData('base_row_total_incl_tax');
            $orderedProductIds[$i]['base_tax_amount'] = (float)$item->getData('base_tax_amount');
            $orderedProductIds[$i]['tax_amount'] = (float)$item->getData('tax_amount');
            $orderedProductIds[$i]['base_tax_invoiced'] = (float)$item->getData('base_tax_invoiced');
            $orderedProductIds[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
            $orderedProductIds[$i]['discount_percent'] = (float)$item->getData('discount_percent');
            $orderedProductIds[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
            $orderedProductIds[$i]['rewardpoints_base_discount'] = (float)$item->getData('rewardpoints_base_discount');
            $orderedProductIds[$i]['free_shipping'] = $item->getData('free_shipping');
            $orderedProductIds[$i]['is_qty_decimal'] = $item->getData('is_qty_decimal');
            $orderedProductIds[$i]['is_virtual'] = $item->getData('is_virtual');
            $orderedProductIds[$i]['original_price'] = (float)$item->getData('original_price');
            $orderedProductIds[$i]['base_original_price'] = (float)$item->getData('base_original_price');
            $orderedProductIds[$i]['price'] = (float)$item->getData('price');
            $orderedProductIds[$i]['price_incl_tax'] = (float)$item->getData('price_incl_tax');
            $orderedProductIds[$i]['product_id'] = $item->getData('product_id');
            $orderedProductIds[$i]['product_type'] = $item->getData('product_type');
            $orderedProductIds[$i]['qty_canceled'] = (float)$item->getData('qty_canceled');
            $orderedProductIds[$i]['qty_invoiced'] = (float)$item->getData('qty_invoiced');
            $orderedProductIds[$i]['qty_ordered'] = (float)$item->getData('qty_ordered');
            $orderedProductIds[$i]['qty_refunded'] = (float)$item->getData('qty_refunded');
            $orderedProductIds[$i]['qty_shipped'] = (float)$item->getData('qty_shipped');
            $orderedProductIds[$i]['quote_item_id'] = $item->getData('quote_item_id');
            $orderedProductIds[$i]['row_invoiced'] = $item->getData('row_invoiced');
            $orderedProductIds[$i]['row_total'] = (float)$item->getData('row_total');
            $orderedProductIds[$i]['row_total_incl_tax'] = (float)$item->getData('row_total_incl_tax');
            $orderedProductIds[$i]['row_weight'] = $item->getData('row_weight');
            $orderedProductIds[$i]['sku'] = $item->getData('sku');
            $orderedProductIds[$i]['store_id'] = $item->getData('store_id');
            $orderedProductIds[$i]['tax_invoiced'] = (float)$item->getData('tax_invoiced');
            $orderedProductIds[$i]['tax_percent'] = (float)$item->getData('tax_percent');
            $orderedProductIds[$i]['updated_at'] = $item->getData('updated_at');
            $orderedProductIds[$i]['order_id'] = $order->getId();
            $i++;
        }
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $payment = $helper->getPayment($order);
        $itemInfoBuy = $helper->getItemsInfoBuy($order);
        $commentsHistory = $order->getStatusHistoryCollection()->addAttributeToSort('created_at', 'DESC');
        $comments = array();
        $j = 0;
        foreach ($commentsHistory as $comment) {
            $comments[$j]['comment'] = $comment->getComment();
            $comments[$j]['created_at'] = $comment->getCreatedAt();
            $j++;
        }
        $orderData = $helper->getOrderData($order);
        $orderData['items'] = $orderedProductIds;
        $orderData['status_histories'] = $comments;    // Comments history
        $orderData['items_info_buy']['items'] = $itemInfoBuy;  // Info items to reorder
        $orderData['billing_address'] = $billingAddress->getData();
        $orderData['payment'] = $payment;
        // Shipping address - output rest api
        if ($shippingAddress)
            $orderData['extension_attributes']['shipping_assignments'][]['shipping']['address'] = $shippingAddress->getData();

        return $orderData;
    }

    public function getOrders24h()
    {
        $year = $this->getRequest()->getParam('year');
        $month = $this->getRequest()->getParam('month');
        $day = $this->getRequest()->getParam('day');

        // first get stores
        $stores_q = "";
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $_eachStoreId => $val) {
            $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
            if (strlen($stores_q) > 0)
                $stores_q = $stores_q . ",";
            $stores_q = $stores_q . "((SELECT count(*) FROM sales_flat_order where store_id=$_storeId and YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day  and order_status!='canceled')) as '$_storeName'";
        }

        $total_query = "SELECT count(*) as 'Total Of Day', $stores_q FROM sales_flat_order where YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day and order_status!='canceled'";

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($total_query)[0];

        $result["totals"] = $results;

        $hour_query = "SELECT  HOUR(shipping_delivery_date) as thehour,count(*) as count_in_hour FROM sales_flat_order where YEAR(shipping_delivery_date)=$year and MONTH(shipping_delivery_date)=$month and DAY(shipping_delivery_date)=$day group by thehour";
        $hour_results = $readConnection->fetchAll($hour_query);
        $hourly_results = [];


        $previous_hour = -1;

        foreach ($hour_results as $hour_result) {
            $h = $hour_result['thehour'];
            if ($h > $previous_hour) {
                for ($x = $previous_hour + 1; $x < $h; $x++) {
                    $hour_item['hour'] = $x;
                    $hour_item['label'] = sprintf("%02d", $x) . ":00 to " . sprintf("%02d", $x + 1) . ":00";
                    $hour_item['count'] = 0;
                    $hourly_results[] = $hour_item;
                }
            }
            $previous_hour = $h;
            $ih = (int)$h;
            $hour_item['hour'] = $ih;
            $hour_item['label'] = sprintf("%02d", $ih) . ":00 to " . sprintf("%02d", $ih + 1) . ":00";;
            $hour_item['count'] = (int)$hour_result['count_in_hour'];
            $hourly_results[] = $hour_item;
        }
        if ($previous_hour < 24) {
            for ($x = $previous_hour + 1; $x <= 23; $x++) {
                $hour_item['hour'] = $x;
                $hour_item['label'] = sprintf("%02d", $x) . ":00 to " . sprintf("%02d", $x + 1) . ":00";
                $hour_item['count'] = 0;
                $hourly_results[] = $hour_item;
            }
        }


        $result['hourly'] = $hourly_results;


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

            case self::OPERATION_GET_PAYMENT_METHODS:
                $result = $this->getPaymentMethods();
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

            case self::OPERATION_GET_ORDER_DETAIL:
                try {
                    $result = $this->getOrderDetail();
                    $status = Mage_Api2_Model_Server::HTTP_OK;
                } catch (Exception $exception) {
                    $result = [
                        'error' => 'Error getting order detail',
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