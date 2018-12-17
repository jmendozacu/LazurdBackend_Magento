<?php


class Cac_Restapi_Drivers_Model_Api2_Driver_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_DRIVER_LIST = 'list';
    const OPERATION_ASSIGN_DRIVER_FOR_ORDER = 'assign';
    const OPERATION_GET_DRIVER_ORDER_LIST = 'orders';
    const OPERATION_GET_DRIVER_STATUS = 'status';

    /**
     * Route: /cac/driver/list
     */
    public function getDriverList()
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

    /**
     * Route: /cac/driver/order/assign/:driver_id/?orders=1,2,3,...
     */
    public function assignDriverForOrder()
    {
        $driver_id = $this->getRequest()->getParam('driver_id');
        $orders_list = $this->getRequest()->getPost()["orders"];
        $orders = explode(',', $orders_list);
        if ($driver_id) {
            foreach ($orders as $the_order) {
                $order = Mage::getModel('sales/order')->load($the_order);
                if ($order->getId()) {
                    $order->setDriverId($driver_id);
                    // this should be here, don't know why?! ABD
                    $order->setOrderStatus("dispatched");
                    $this->updateStatusAction($the_order, "dispatched");
                    $order->save();

                    // now send SMS using new method
                    if (Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
                        $customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
                        if ($telephone = $customer->getData("phone")) {
                            $driver_id = $order->getDriverId();
                            $driverdata = Mage::getModel("dirvermanagement/driver")->load($driver_id, 'userid')->getData();
                            $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
                            $order_urls = 'sales/guest/view/order_id/' . Mage::helper('core')->urlEncode($order->getIncrementId());
                            $order_url = Mage::getUrl($order_urls, array('_secure' => true));
                            $username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
                            $password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
                            $msisdn = '965' . $telephone;
                            $unicode_msg = str_replace('{{order link}}', $order_url . ' Driver Phone ' . $driverdata[mobile], Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
                            $post_body = $this->seven_bit_sms($username, $password, $unicode_msg, $msisdn);
                            $result = $this->send_message($post_body, $url);
                            if ($result['success']) {
                                //$data->setIsCustomerNotify('1');
                            } else {
                                //$data->setIsCustomerNotify('0');
                            }
                        }

                    }
                }
            }
        }

        return ["status" => "Driver $driver_id assigned to orders : " . $orders_list];
    }

    /**
     * Route: /cac/driver/order/list/:driver_id/
     */
    public function getDriverOrders()
    {
        $driver_id = $this->getRequest()->getParam('driver_id');
        $start_date = $this->getRequest()->getParam('start_date');
        $end_date = $this->getRequest()->getParam('end_date');
        if ($start_date == null) {
            $start_date = "2000/01/01";
        }
        if ($end_date == null) {
            $end_date = "2099/01/01";
        }

        list($year_s, $month_s, $day_s) = explode("/", $start_date);
        list($year_e, $month_e, $day_e) = explode("/", $end_date);

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "SELECT * from sales_flat_order as sfo left join driver_delivery_information as ddi on sfo.increment_id=ddi.order_id  where sfo.driver_id=$driver_id and (sfo.shipping_delivery_date >= '$year_s-$month_s-$day_s 0:0' AND sfo.shipping_delivery_date <= '$year_e-$month_e-$day_e 23:59')";
        $orders = $readConnection->fetchAll($query);

        $all_orders = [];
        $counter = 0;

        try {
            foreach ($orders as $order) {
                $this_order = [];
//                $items = [];

                $this_order["OrderNo"] = $order["increment_id"];
                $this_order["EntityID"] = $order["entity_id"];
                $this_order["OrderDate"] = $order["shipping_arrival_date"];
                $this_order["OrderStatus"] = $order["order_status"];

                $this_order["CustomerID"] = $order["customer_id"];
                $this_order["CustomerName"] = $order["customer_firstname"] . " " . $order["customer_lastname"];

                $this_order["CashReceived"] = intval($order["cash_received"]);

//                $delivery = [];
//
//                if ($order->getShippingAddress()) {
//                    $delivery["Address"] = $order->getShippingAddress()->getStreetFull() . ", " . $order->getShippingAddress()->getRegion() . ", " . $order->getShippingAddress()->getCity();
//                }
                $delivery["DeliveryDate"] = $order["shipping_delivery_date"];
                $delivery["ArrivalDate"] = $order["shipping_arrival_date"];
                $delivery["ShippingDescription"] = $order["shipping_arrival_comments"];
//                $delivery["DriverID"] = $order->getDriverId();
//                $delivery["DriverName"] = $driver_list[$order->getDriverId()];

//                $this_order["Delivery"] = $delivery;

//                $this_order["OrderItems"] = $items;
                $all_orders[] = $this_order;
                $counter++;
            }
            $results["items"] = $all_orders;
            $results["count"] = count($orders);
            return $results;
        } catch (Exception $e) {
            return [];
        }

        return $results;
    }

    /**
     * Route: /cac/driver/order/status/:driver_id/
     */
    public function getDriverStatus()
    {
        $driver_id = $this->getRequest()->getParam('driver_id');
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select order_status,count(order_status) as count from sales_flat_order where driver_id='$driver_id' group by order_status";
        $orders = $readConnection->fetchAll($query);

        return $orders;
    }

    public function updateStatusAction($fieldId, $assign_status)
    {
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus = array();
        foreach ($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];
        }
//        $fieldId = (int) $this->getRequest()->getParam('id');
//        $assign_status = $this->getRequest()->getParam('assign_status');
        if ($fieldId) {
            $model = Mage::getModel('sales/order')->load($fieldId);
            if ($model->getId()) {
                $comment = '#' . $model->getIncrementId() . " Order status has been changed to " . $customstatus[$assign_status];

                $model->setOrderStatus($assign_status);
                $model->addStatusHistoryComment($comment)
                    ->setIsVisibleOnFront(true)
                    ->setIsCustomerNotified(false)
                    ->setCustomOrderStatus($customstatus[$assign_status]);
                $model->save();
                // Create Shipment
                if ($model->getOrderStatus() == 'delivered') {
                    $this->Creteshipment($model);
                }
                // Cancel an order
                if ($model->getOrderStatus() == 'returned' || $model->getOrderStatus() == 'canceled') {
                    $this->Cancelorder($model);
                }
                //Dispatch and send message
                if ($model->getOrderStatus() == 'dispatched') {
                    if (Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {

                        if ($telephone = $model->getBillingAddress()->getTelephone()) {
                            // now send SMS using new method
                            if (Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
                                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
                                $order_urls = 'sales/guest/view/order_id/' . Mage::helper('core')->urlEncode($model->getIncrementId());
                                $order_url = Mage::getUrl($order_urls, array('_secure' => true));
                                $username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
                                $password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
                                $msisdn = '965' . $telephone;
                                $unicode_msg = str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage'));
                                $post_body = $this->seven_bit_sms($username, $password, $unicode_msg, $msisdn);
                                $result = $this->send_message($post_body, $url);
                                if ($result['success']) {
                                    //$data->setIsCustomerNotify('1');
                                } else {
                                    //$data->setIsCustomerNotify('0');
                                }
                                Mage::log(print_r($result, 1), null, 'mylog3.log');

                            }


//                            $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
//
//                            Mage::log($order_url, null, 'mylog3.log');
//                            $fields = array(
//                                'username' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname'),
//                                'password' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd'),
//                                'customerid' => '361',
//                                'sendertext' => 'HiNet GCC',
//                                'messagebody' => str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/dispatchmessage')),
//                                'recipientnumbers' => '965' . $telephone, // '96550618808',
//                                'defdate' => '', //Mage::getModel('core/date')->date('Y-m-d'),
//                                'isblink' => 'false',
//                                'isflash' => 'false'
//                            );
//                            $field = http_build_query($fields); // encode array to POST string
//                            //Mage::log(print_r($field, 1), null, 'mylog3.log');
//                            $post = curl_init();
//                            curl_setopt($post, CURLOPT_URL, $url);
//                            curl_setopt($post, CURLOPT_POST, 1);
//                            curl_setopt($post, CURLOPT_POSTFIELDS, $field);
//                            curl_setopt($post, CURLOPT_USERAGENT, 'Mozilla/5.0');
//                            curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
//                            $result = curl_exec($post);
//                            // for debugging
//                            $sxml = new SimpleXMLElement($result);
//                            $Result = $this->xml2array($sxml);
//
//                            if ($Result['Result'] == 'true') {
//                                Mage::log($model->getIncrementId(), null, 'mylog3.log');
//                                Mage::log('Success', null, 'mylog3.log');
//                                //$data->setIsCustomerNotify('1');
//                            } else {
//                                Mage::log($model->getIncrementId(), null, 'mylog3.log');
//                                Mage::log('Failed', null, 'mylog3.log');
//                                //$data->setIsCustomerNotify('0');
//                            }
//                            curl_close($post);
//                            Mage::log(print_r($result, 1), null, 'mylog3.log');
                        }
                    }
                }
            }
        }
    }

    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_DRIVER_LIST:
                $result = $this->getDriverList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_ASSIGN_DRIVER_FOR_ORDER:
                $result = $this->assignDriverForOrder();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_DRIVER_ORDER_LIST:
                $result = $this->getDriverOrders();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_DRIVER_STATUS:
                $result = $this->getDriverStatus();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }


    // new SMS method

    function send_message($post_body, $url)
    {
        /*
        * Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn it into in a
        * multipart formpost, which is not supported:
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        // Allowing cUrl funtions 20 second to execute
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // Waiting 20 seconds while trying to connect
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

        $response_string = curl_exec($ch);
        $curl_info = curl_getinfo($ch);

        $sms_result = array();
        $sms_result['success'] = 0;
        $sms_result['details'] = '';
        $sms_result['transient_error'] = 0;
        $sms_result['http_status_code'] = $curl_info['http_code'];
        $sms_result['api_status_code'] = '';
        $sms_result['api_message'] = '';
        $sms_result['api_batch_id'] = '';

        if ($response_string == FALSE) {
            $sms_result['details'] .= "cURL error: " . curl_error($ch) . "\n";
        } elseif ($curl_info['http_code'] != 200) {
            $sms_result['transient_error'] = 1;
            $sms_result['details'] .= "Error: non-200 HTTP status code: " . $curl_info['http_code'] . "\n";
        } else {
            $sms_result['details'] .= "Response from server: $response_string\n";
            $api_result = explode('|', $response_string);
            $status_code = $api_result[0];
            $sms_result['api_status_code'] = $status_code;
            $sms_result['api_message'] = $api_result[1];
            if (count($api_result) != 3) {
                $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count($api_result);
            } else {
                if ($status_code == '0') {
                    $sms_result['success'] = 1;
                    $sms_result['api_batch_id'] = $api_result[2];
                    $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
                } else if ($status_code == '1') {
                    # Success: scheduled for later sending.
                    $sms_result['success'] = 1;
                    $sms_result['api_batch_id'] = $api_result[2];
                } else {
                    $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
                }


            }
        }
        curl_close($ch);

        return $sms_result;
    }


    function seven_bit_sms($username, $password, $message, $msisdn)
    {
        $post_fields = array(
            'username' => $username,
            'password' => $password,
            'message' => $this->character_resolve($message),
            'msisdn' => $msisdn,
            'allow_concat_text_sms' => 0, # Change to 1 to enable long messages
            'concat_text_sms_max_parts' => 2
        );

        return $this->make_post_body($post_fields);
    }

    function unicode_sms($username, $password, $message, $msisdn)
    {
        $post_fields = array(
            'username' => $username,
            'password' => $password,
            'message' => $this->string_to_utf16_hex($message),
            'msisdn' => $msisdn,
            'dca' => '16bit',
            'allow_concat_text_sms' => 1
        );

        return $this->make_post_body($post_fields);
    }

    function make_post_body($post_fields)
    {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
            $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach ($post_fields as $key => $value) {
            $post_body .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $post_body = rtrim($post_body, '&');

        return $post_body;
    }


    /*
    * Unique ID to eliminate duplicates in case of network timeouts - see
    * EAPI documentation for more. You may want to use a database primary
    * key. Warning: sending two different messages with the same
    * ID will result in the second being ignored!
    *
    * Don't use a timestamp - for instance, your application may be able
    * to generate multiple messages with the same ID within a second, or
    * part thereof.
    *
    * You can't simply use an incrementing counter, if there's a chance that
    * the counter will be reset.
    */
    function make_stop_dup_id()
    {
        return 0;
    }

    function string_to_utf16_hex($string)
    {
        return bin2hex(mb_convert_encoding($string, "UTF-16", "UTF-8"));
    }

    function character_resolve($body)
    {
        $special_chrs = array(
            'Δ' => '0xD0', 'Φ' => '0xDE', 'Γ' => '0xAC', 'Λ' => '0xC2', 'Ω' => '0xDB',
            'Π' => '0xBA', 'Ψ' => '0xDD', 'Σ' => '0xCA', 'Θ' => '0xD4', 'Ξ' => '0xB1',
            '¡' => '0xA1', '£' => '0xA3', '¤' => '0xA4', '¥' => '0xA5', '§' => '0xA7',
            '¿' => '0xBF', 'Ä' => '0xC4', 'Å' => '0xC5', 'Æ' => '0xC6', 'Ç' => '0xC7',
            'É' => '0xC9', 'Ñ' => '0xD1', 'Ö' => '0xD6', 'Ø' => '0xD8', 'Ü' => '0xDC',
            'ß' => '0xDF', 'à' => '0xE0', 'ä' => '0xE4', 'å' => '0xE5', 'æ' => '0xE6',
            'è' => '0xE8', 'é' => '0xE9', 'ì' => '0xEC', 'ñ' => '0xF1', 'ò' => '0xF2',
            'ö' => '0xF6', 'ø' => '0xF8', 'ù' => '0xF9', 'ü' => '0xFC',
        );

        $ret_msg = '';
        if (mb_detect_encoding($body, 'UTF-8') != 'UTF-8') {
            $body = utf8_encode($body);
        }
        for ($i = 0; $i < mb_strlen($body, 'UTF-8'); $i++) {
            $c = mb_substr($body, $i, 1, 'UTF-8');
            if (isset($special_chrs[$c])) {
                $ret_msg .= chr($special_chrs[$c]);
            } else {
                $ret_msg .= $c;
            }
        }
        return $ret_msg;
    }

    // end of new SMS method

}