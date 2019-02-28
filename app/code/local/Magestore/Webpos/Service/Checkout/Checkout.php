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
class Magestore_Webpos_Service_Checkout_Checkout extends Magestore_Webpos_Service_Abstract
{
    /**
     * Magestore_Webpos_Service_Checkout_Checkout constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_responseService = $this->_createService('checkout_response');
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array|string $section
     * @return mixed
     */
    public function getCartData($quoteData, $section)
    {
        $data = array();
        $message = array();
        if (!empty($quoteData) && is_array($quoteData)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_finishAction(false);
            if (is_array($section)) {
                $section[] = Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT;
            } else {
                $section = array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, $section);
            }
            $data = $this->_getQuoteData($section, $orderCreateModel);
            $message = $this->_getQuoteErrors($orderCreateModel);
        }
        $status = (empty($message)) ? Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS : Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return mixed
     */
    public function removeCart($quoteData)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($quoteData)) {
            $orderCreateModel = $this->getCheckoutModel();
            $eventData = array(
                'quote' => $this->getQuote()
            );
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_BEFORE, $eventData);
            $orderCreateModel->removeQuote($quoteData);
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT] = array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID => '',
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID => ''
            );
            $this->_assignQuoteToStaff(array());
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_AFTER, array());
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $itemId
     */
    public function removeItem($quoteData, $itemId)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($itemId)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->removeQuoteItem($itemId);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param Magestore_Webpos_Api_Cart_ItemRequestInterface[] $buyRequests
     * @param array $customerData
     * @param array $updateSections
     * @return mixed
     */
    public function saveCart($quoteData, $buyRequests, $customerData, $updateSections)
    {
        $data = array();
        $message = array();
        if (!empty($buyRequests) && is_array($buyRequests)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_saveCart($buyRequests, $orderCreateModel);
            $this->_setCustomer($customerData, $orderCreateModel);
            $this->_setDefaultData($orderCreateModel);
            $eventData = array(
                'quote' => $this->getQuote()
            );
            $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_SAVE_CART_AFTER, $eventData);
            $this->_finishAction();
            $data = $this->_getQuoteData($updateSections, $orderCreateModel);
            $message = $this->_getQuoteErrors($orderCreateModel);
        }
        $status = (empty($message)) ? Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS : Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $customerData
     * @return mixed
     */
    public function selectCustomer($quoteData, $customerData)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($customerData) && is_array($customerData)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_setCustomer($customerData, $orderCreateModel);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $method
     * @return mixed
     */
    public function saveShippingMethod($quoteData, $method)
    {

        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($method)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $this->_getCheckoutApi('shipping')->setShippingMethod($orderCreateModel->getQuote()->getId(), $method);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS
            ), $orderCreateModel);
        }

        Mage::log('data ', null, 'mylog3.log');

        return $this->getResponseData($data, $message, $status);
    }


    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $method
     * @return mixed
     */
    public function savePaymentMethod($quoteData, $method)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($method)) {
            $payment = array(Magestore_Webpos_Api_Checkout_PaymentInterface::METHOD => $method);
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->setPaymentData($payment);
            $orderCreateModel->getQuote()->getPayment()->addData($payment);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(
                Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS
            ), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $fields
     * @return mixed
     */
    public function saveQuoteData($quoteData, $fields)
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($fields)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $orderCreateModel->addQuoteData($fields);
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string $couponCode
     * @return mixed
     */
    public function applyCoupon($quoteData, $couponCode)
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($couponCode)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $quote = $orderCreateModel->getQuote();
            try {
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals();
            } catch (Exception $e) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $e->getMessage();
            }
            if (!$couponCode == $quote->getCouponCode()) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $this->__('Coupon code is not valid');
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        } else {
            return $this->cancelCoupon($quoteData);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return mixed
     */
    public function cancelCoupon($quoteData)
    {
        $data = array();
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($quoteData)) {
            $orderCreateModel = $this->_startAction($quoteData);
            $quote = $orderCreateModel->getQuote();
            try {
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setCouponCode('')->collectTotals();
            } catch (Exception $e) {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $e->getMessage();
            }
            $this->_finishAction();
            $data = $this->_getQuoteData(array(), $orderCreateModel);
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param array $payment
     * @param array $fields
     * @param array $actions
     * @param array $integration
     * @return mixed
     */
    public function placeOrder($quoteData, $payment, $fields, $actions, $integration)
    {

        $data = array();
        $message = array();
        // $message[] = $actions;
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($quoteData)) {
            $orderCreateModel = $this->_startAction($quoteData);
            if (!empty($fields)) {
                $orderCreateModel->addQuoteData($fields);
            }
            if (Mage::getVersion() >= '1.8') {
                if (isset($payment) && !empty($payment)) {
                    /*Discount item 100%*/
                    if ($this->getQuote()->getGrandTotal() == 0) {
                        $payment = array('method' => 'free');
                    }
                    /*Discount item 100%*/
                    $payment['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                        | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                        | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

                    $orderCreateModel->setPaymentData($payment);
                    Mage::log($payment, null, 'Checkout-log.log');
                    $orderCreateModel->getQuote()->getPayment()->addData($payment);
                }
            }
            $order = $orderCreateModel
                ->setIsValidate(true)
                ->createOrder();
            if ($order && $order->getId()) {
                $orderCreateModel->processPaymentAfterCreateOrder($order, $payment);
                $orderCreateModel->processActionsAfterCreateOrder($order, $actions);

                $orderCreateModel->processIntegration($order, $integration);

                // Edit by Jacob

                if (Mage::helper('core')->isModuleEnabled('Cmsmart_Deliverydate')) {
                    $delivery = Mage::getModel('deliverydate/deliverydate')->load($actions['shipping_arrival_time_slot']);

                    $deliverydate = '';
                    if ($delivery->getData()) {
                        $deliverydate = $delivery['fromtime'] . ' - ' . $delivery['totime'];
                    }
                    if (isset($actions['shipping_arrival_date']) && !empty($actions['shipping_arrival_date'])):
                        $arrival_date = date('Y-m-d ', strtotime($actions['shipping_arrival_date']));
                        $ShippingArrivalDate = date('Y-m-d ', strtotime($actions['shipping_arrival_date'])) . ' ' . $deliverydate;
                        $ShippingDeliveryDate_ori = date('Y-m-d ', strtotime($actions['shipping_arrival_date'])) . ' ' . $delivery['totime'];
                        $ShippingDeliveryDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $ShippingDeliveryDate_ori);

                        $order->setShippingArrivalDate($ShippingArrivalDate);
                        $order->setShippingDeliveryDate($ShippingDeliveryDate);
                        $order->setShippingArrivalTimeSlot($actions['shipping_arrival_time_slot']);
                    endif;
                    $order->setShippingArrivalComments($actions['shipping_arrival_comments']);


                    // $order->setShippingArrivalDate($actions['shipping_arrival_date'] . ' ' . $deliverydate);
                    // $order->setShippingDeliveryDate($actions['shipping_arrival_date']);
                    // $order->setShippingArrivalComments($actions['shipping_arrival_comments']);
                    // $order->setshippingArrivalTimeSlot($actions['shipping_arrival_time_slot']);

                    // $config['deliverydate'] = $delivery->getData($actions['shipping_arrival_time_slot']);
                }

                // End
                if ($actions[Magestore_Webpos_Api_Checkout_ConfigInterface::KEY_CREATE_SHIPMENT] == false) {
                    if ($actions['need_watting'] || $actions['need_approval']) {
                        if ($actions['need_approval']) {
                            $order->setStatus('need_approval');
                            $order->setOrderStatus('pending');
                        }
                        if ($actions['need_watting']) {
                            $order->setStatus('waitting');
                            $order->setOrderStatus('waitting');
                        }
                        if ($actions['need_watting'] && $actions['need_approval']) {
                            $order->setStatus('waitting_need_approve');
                        }
                    } else {
                        $order->setOrderStatus('approved');
                    }
                    $order->save();

                    if ($actions['need_approval']) {

                        $this->_sendEmailOrderNeedAproval($order);
                    }
                }
                // End

                $orderCreateModel->sendEmail($order);
                $data = $this->_responseService->getOrderSuccessData($order);
                // Islam Elgarhy Print As Magento 
                $store = Mage::getModel('core/store')->load($order->getData('store_id'));
                $name = $store->getName();
                $data['store_nameOnly'] = $name;

                $customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
                $data['customer_phone_original'] = $customer->getData("phone");
                //
                $this->_assignQuoteToStaff(false);
                // CAC
                // ABD
//                $sessionModel = $this->_getCurrentStaffSession();
//                $sessionModel = ($sessionModel)?$sessionModel:$this->_getCurrentStaffSession();
//                Mage::log("payment : " . json_encode( $payment), null, 'mylog_staff_quote.log');
                // ABD from CAC
                // WRITE DOWN SALES STAFF HISTORY


                // TODO: Create this table in DB
//                CREATE TABLE `cac_staff_sales_history` (
//                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//  `staff_id` int(11) unsigned NOT NULL DEFAULT '0',
//  `order_increment_id` varchar(255) DEFAULT '0',
//  `created_at` datetime DEFAULT  now(),
//  `amount` float DEFAULT '0',
//  `base_amount` float DEFAULT '0',
//  `transaction_currency_code` varchar(255) NOT NULL,
//  `base_currency_code` varchar(255) NOT NULL,
//  `note` text,
//  `is_manual` smallint(6) NOT NULL DEFAULT '1',
//  `is_opening` smallint(6) NOT NULL DEFAULT '0',
//  `status` smallint(6) NOT NULL DEFAULT '1',
//  PRIMARY KEY (`id`)
//) ENGINE=InnoDB AUTO_INCREMENT=2270 DEFAULT CHARSET=utf8;


//                $resource = Mage::getSingleton('core/resource');
//                $readConnection = $resource->getConnection('core_read');
//
//                for ($pc=0;$pc<sizeof($payment['method_data']);$pc++) {
//
//                    $insert_query = "INSERT INTO cac_staff_sales_history
//(
//`staff_id`,
//`order_increment_id`,
//`created_at`,
//`amount`,
//`base_amount`,
//`transaction_currency_code`,
//`base_currency_code`,
//`note`,
//`is_manual`,
//`is_opening`,
//`status`)
//VALUES
//(
//" . $order->getData('webpos_staff_id') . ",
//'" . $order->getData('increment_id') . "',
//now(),
//" . $payment['method_data'][$pc]['real_amount'] . ",
//" . $payment['method_data'][$pc]['base_real_amount'] . ",
//'" . $order->getData('order_currency_code') . "',
//'" . $order->getData('base_currency_code') . "',
//'',
//1,
//0,
//1)
//ON DUPLICATE KEY UPDATE amount=" . $order->getData('webpos_staff_id') . ",base_amount=" . $payment['method_data'][0]['base_real_amount'];
//                    $results = $readConnection->query($insert_query);
//                }
                // END CAC

            } else {
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                $message[] = $order;
            }
        }
        $baseUrl = Mage::getBaseUrl();
        $token = "";
        $toTopic = "";
        $title = "";
        $body = "";
        $click_action = "";
        $icon = "";
        if ($baseUrl == "https://kwt.lazurd.com/index.php/") {
            $toTopic = "KWT_ORDER";
            $token = "";
            $title = "";
            $body = "";
            $click_action = "";
            $icon = "";
        } else if ($baseUrl == "https://ksa.lazurd.com/index.php/") {
            $toTopic = "KSA_ORDER";
            $token = "";
            $title = "";
            $body = "";
            $click_action = "";
            $icon = "";
        } else if ($baseUrl == "https://lazurd.adad.ws/index.php/") {
            $toTopic = "DEV_ORDER";
            $token = "AAAAFYUJun4:APA91bGyZlqmvwRAN5VxgIYnesc33SXPjNolQW0fwPYnIxe3VlzYN-KtzOndfmAvSB7vvPSiaJGV1zdE3aatBedklzJnRVKxz6ubgiCuxycGOV1kQAwA6Ii47ziehoy4snD2WIMqwBGa";
            $title = "ORDER PLACED";
            $body = "Please Open your Dashboard";
            $click_action = "https://lazurdreport.adad.ws";
            $icon = "https://lazurdreport.adad.ws/assets/images/logo-menu.png";
        } else if ( $baseUrl == "http://lazurd.localhost/index.php/") {
            $toTopic = "LOCAL_ORDER";
            $token = "AAAAFYUJun4:APA91bGyZlqmvwRAN5VxgIYnesc33SXPjNolQW0fwPYnIxe3VlzYN-KtzOndfmAvSB7vvPSiaJGV1zdE3aatBedklzJnRVKxz6ubgiCuxycGOV1kQAwA6Ii47ziehoy4snD2WIMqwBGa";
            $title = "ORDER PLACED";
            $body = "Please Open your Dashboard";
            $click_action = "https://report.localhost:4200";
            $icon = "https://report.localhost:4200/assets/images/logo-menu.png";
        }


        $url = "https://fcm.googleapis.com/fcm/send";
        $post_body = [
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => $click_action,
                'icon' => $icon
            ],
            'data' => [

            ],
            'content_available' => true,
            'priority' => 'high',
            'to' => $toTopic
        ];
        $data_string = json_encode($post_body);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "key=".$token,
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
        // TODO: Error in this line :  Fatal error: Uncaught Error: Class 'Zend\Http\Headers' not found
        /*
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'key=' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://fcm.googleapis.com/fcm/send');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        $params = new \Zend\Stdlib\Parameters([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => $click_action,
                'icon' => $icon
            ],
            'data' => [

            ],
            'content_available' => true,
            'priority' => 'high',
            'to' => $toTopic
        ]);

        $request->setQuery($params);
        $client = new \Zend\Http\Client();
        $options = [
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $client->setOptions($options);

        $response = $client->send($request);
        */
        return $this->getResponseData($data, $message, $status);
    }

    // Edit by Jacob

    protected function _sendEmailOrderNeedAproval($order)
    {

        $store = Mage::app()->getStore();

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $recipientName = $store->getFrontendName();

        $listRecipientEmails = explode(',', Mage::getStoreConfig('webpos/email_configuration/list_email_order_need_approval', $store));

        foreach ($listRecipientEmails as $recipientEmail) {

            if ($recipientEmail) {
                Mage::getModel('core/email_template')
                    ->setDesignConfig(array(
                            'area' => 'frontend',
                            'store' => $store->getId())
                    )
                    ->sendTransactional(
                        Mage::getStoreConfig('webpos/email_configuration/email_template_order_approval', $store),
                        Mage::getStoreConfig('trans_email/ident_general', $store),
                        $recipientEmail,
                        $recipientName,
                        array(
                            'store' => $store,
                            'order' => $order,
                            'recipient_name' => $recipientName,
                            'recipient_email' => $recipientEmail
                        )
                    );
            }

        }

        $translate->setTranslateInline(true);
    }

    // End Jacob

    /**
     *
     * @param string $customerId
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface[] $items
     * @param Magestore_Webpos_Api_Checkout_PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode = "")
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        $checkout = $this->getCheckoutModel();
        $data = $checkout->checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode);
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $orderIncrementId
     * @param $customerEmail
     * @return mixed
     */
    public function sendOrderEmail($orderIncrementId, $customerEmail)
    {
        $message = array();
        $checkout = $this->getCheckoutModel();
        $data = $checkout->sendEmail($orderIncrementId, $customerEmail);
        $status = ($data['error'] == true) ? Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR : Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        if (!empty($data['message'])) {
            $message[] = $data['message'];
        };
        return $this->getResponseData($data, $message, $status);
    }

    /**
     *
     * @param string $customerId
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface[] $items
     * @param Magestore_Webpos_Api_Checkout_PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function syncOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration)
    {
        $message = array();
        $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        $checkout = $this->getCheckoutModel();
        $order = $checkout->prepareOrder($customerId, $items, $payment, $shipping, $config, $couponCode, $extensionData, $sessionData, $integration);
        $data = ($order) ? $this->_responseService->getOrderSuccessData($order) : array();
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $sections
     * @param $model
     * @return array
     */
    protected function _getQuoteData($sections, $model)
    {
        $data = array();
        $orderCreateModel = ($model) ? $model : $this->getCheckoutModel();
        if (empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT, $sections))) {
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_INIT] = $orderCreateModel->getQuoteInitData();
        }
        if (empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS, $sections))) {
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::ITEMS] = $this->_responseService->getQuoteItems();
        }
        if (empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS, $sections))) {
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::TOTALS] = $this->_responseService->getTotals();
        }
        if (empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING, $sections))) {
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING] = $this->_responseService->getShipping();
        }
        if (empty($sections) || $sections == Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT || (is_array($sections) && in_array(Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT, $sections))) {
            $data[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::PAYMENT] = $this->_responseService->getPayment();
        }
        return $data;
    }

    /**
     * @param $buyRequests
     * @param $orderCreateModel
     */
    protected function _saveCart($buyRequests, $orderCreateModel)
    {
        $newItems = array();
        $updateItems = array();
        foreach ($buyRequests as $request) {
            $itemId = $request->getItemId();
            $item = $orderCreateModel->getQuoteItem($itemId);
            if ($item) {
                $updateItems[$itemId] = $request->getData();
            } else {
                if ($request->getIsCustomSale()) {
                    $options = $request->getOptions();
                    $taxClassId = isset($options['tax_class_id']) ? $options['tax_class_id'] : '';
                    $product = $this->_helper->createCustomSaleProduct($taxClassId);
                    if ($product instanceof Mage_Catalog_Model_Product) {
                        $request->setId($product->getId());
                    }
                }
                $newItems[] = $request->getData();
            }
        }
        if (!empty($newItems)) {
            $orderCreateModel->addProducts($newItems);
        }
        if (!empty($updateItems)) {
            $orderCreateModel->updateQuoteItems($updateItems);
        }
    }

    /**
     * @param $customerData
     * @param $orderCreateModel
     * @return bool|string
     */
    protected function _setCustomer($customerData, $orderCreateModel)
    {
        $result = true;
        try {
            $customerId = (is_array($customerData) && !empty($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID]))
                ? $customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID]
                : $this->_config->getDefaultCustomerId();
            if ($customerId) {
                $customer = $this->_getModel('customer/customer')->load($customerId);
                if ($customer->getId()) {
                    $orderCreateModel->getSession()->setCustomerId($customerId);
                    $orderCreateModel->getQuote()->setCustomerId($customerId);
                    $orderCreateModel->getSession()->setCustomer($customer);
                    $orderCreateModel->getQuote()->setCustomer($customer);
                }
            }
            if (isset($customerData) && isset($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS])) {
                $orderCreateModel->setBillingAddress($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::BILLING_ADDRESS]);
            } else {
                $orderCreateModel->useDefaultAddresses('billing');
            }
            if (isset($customerData) && isset($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS])) {
                $orderCreateModel->setShippingAddress($customerData[Magestore_Webpos_Api_Cart_QuoteDataInitInterface::SHIPPING_ADDRESS]);
                $orderCreateModel->getShippingAddress()->setCollectShippingRates(true)->setSameAsBilling(0);
                $reCollectTotal = true;
            } else {
                $orderCreateModel->useDefaultAddresses('shipping');
                $reCollectTotal = false;
            }
            if ($reCollectTotal) {
                $orderCreateModel->getQuote()->collectTotals();
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $orderCreateModel
     * @return bool|string
     */
    protected function _setDefaultData($orderCreateModel)
    {
        $result = true;
        try {
            $defaultPaymentMethod = $this->_config->getDefaultPaymentMethod();
            $defaultShippingMethod = $this->_config->getDefaultShippingMethod();
            $payment = array(Magestore_Webpos_Api_Checkout_PaymentInterface::METHOD => $defaultPaymentMethod);
            $paymentMethod = $orderCreateModel->getQuote()->getPayment()->getMethod();
            if (!isset($paymentMethod)) {
                $orderCreateModel->setPaymentData($payment);
                $orderCreateModel->getQuote()->getPayment()->addData($payment);
            }
            if (!$orderCreateModel->getQuote()->isVirtual()) {
                if (!$orderCreateModel->getQuote()->getShippingAddress()->getShippingMethod()) {
                    $orderCreateModel->setShippingMethod($defaultShippingMethod);
                }
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $model
     * @return array
     */
    protected function _getQuoteErrors($model = false)
    {
        $messages = array();
        $orderCreateModel = ($model) ? $model : $this->getCheckoutModel();
        $quote = $orderCreateModel->getQuote();
        $items = $quote->getAllVisibleItems();
        if (!empty($items)) {
            $oldSuperMode = $quote->getIsSuperMode();
            $quote->setIsSuperMode(false);
            foreach ($items as $item) {
                $item->setQty($item->getQty());
                $stockItem = $item->getProduct()->getStockItem();
                if ($stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                    $check = $stockItem->checkQuoteItemQty($item->getQty(), $item->getQty(), $item->getQty());
//                    $messages[] = $check->getMessage();
                }
            }
            $quote->setIsSuperMode($oldSuperMode);
        }
        $errors = $quote->getErrors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $messages[] = $error->getText();
            }
        }
        return $messages;
    }

    // End Ryan Edit

    /**
     * Prepare Sql
     * @param array $ids
     * @return array
     */
    public function prepareSql($ids = [])
    {
        $ids = implode(",", $ids);
        $sql = [];
        $resource = $this->orderResource;

        /*DELETE All Related Invoice Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids}));";

        /*DELETE all invoice comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids}));";

        /*Delete All invoice in invoice grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice_grid')}
            WHERE order_id IN({$ids});";

        /*Delete All invoices*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_invoice')}
            WHERE order_id IN({$ids});";


        /*DELETE All Related Shipment Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";

        /*DELETE all shipment comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";

        /*DELETE all shipment tracks*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_track')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids}));";

        /*Delete All shipments in shipment grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment_grid')}
            WHERE order_id IN({$ids});";

        /*Delete All shipments*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_shipment')}
            WHERE order_id IN({$ids});";

        /*DELETE All Related Creditmemo Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_item')}
            WHERE parent_id IN (SELECT entity_id FROM
            {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids}));";

        /*DELETE all creditmemo comment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_comment')}
            WHERE parent_id IN
            (SELECT entity_id FROM {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids}));";

        /*Delete All creditmemos in creditmemo grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo_grid')}
            WHERE order_id IN({$ids});";

        /*Delete All shipments*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_creditmemo')}
            WHERE order_id IN({$ids});";


        /*DELETE all order tax item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_tax_item')}
            WHERE tax_id IN
            (SELECT tax_id FROM {$resource->getTable('sales_order_tax')}
            WHERE order_id IN({$ids}));";

        /*DELETE all order tax*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_tax')}
            WHERE order_id IN({$ids});";

        /*DELETE All Related order Item*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_item')}
            WHERE order_id IN({$ids});";

        /*DELETE all order payment*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_payment')}
            WHERE parent_id IN({$ids});";

        /*DELETE all order status history*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_status_history')}
            WHERE parent_id IN({$ids});";

        /*DELETE all order address*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_address')}
            WHERE parent_id IN({$ids});";

        /*Delete All order in order grid*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order_grid')}
            WHERE entity_id IN({$ids});";

        /*Delete All orders*/
        $sql[] = "DELETE FROM {$resource->getTable('sales_order')}
            WHERE entity_id IN({$ids});";

        return $sql;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $ids = $this->getRequest()->getParam('selected');
            if (!$ids || !is_array($ids) || !sizeof($ids))
                throw new \Exception(__("Please select an item to process."));

            $sqls = $this->prepareSql($ids);
            foreach ($sqls as $sql) {
                $this->orderResource->getConnection()->query($sql);
            }
            $this->messageManager->addSuccess(
                __('We deleted %1 order(s).', sizeof($ids))
            );

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order');
    }

    // End Ryan Edit


}
