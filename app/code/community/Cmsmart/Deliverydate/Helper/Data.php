<?php

    class Cmsmart_Deliverydate_Helper_Data extends Mage_Core_Helper_Abstract
    {
        public function getFormatedDeliveryDate($date = null)
        {
            //if null or 0-0-0 00:00:00 return no date string
            if(empty($date) ||$date == null || $date == '0000-00-00 00:00:00'){
                return Mage::helper('deliverydate')->__("No Delivery Date Specified.");
            }

            //Format Date
            $formatedDate = Mage::helper('core')->formatDate($date, 'medium');
            //TODO: check that date is valid before passing it back

            return $formatedDate; 
        }

        public function getFormatedDeliveryDateToSave($date = null)
        {
            if(empty($date) ||$date == null || $date == '0000-00-00 00:00:00'){
                return null;
            }

            $timestamp = null;
            try{
                //TODO: add Better Date Validation
                $timestamp = strtotime($date);
                $dateArray = explode("-", $date);
                if(count($dateArray) != 3){
                    //invalid date
                    return null;
                }
                //die($timestamp."<<");
                //$formatedDate = date('Y-m-d H:i:s', strtotime($timestamp));
                //$formatedDate = date('Y-m-d H:i:s', mktime(0, 0, 0, $dateArray[0], $dateArray[1], $dateArray[2]));
                $formatedDate = date('Y-m-d H:i:s',strtotime($date));
            } catch(Exception $e){
                //TODO: email error 
                //return null if not converted ok
                return null;
            }                

            return $formatedDate;
        }
          public function saveShippingArrivalDate($observer){

            $order = $observer->getEvent()->getOrder();
     
                $cart = Mage::getModel('checkout/cart')->getQuote()->getData();
                $desiredArrivalDate = $cart['shipping_arrival_date'];
                $shipping_arrival_comments = $cart['shipping_arrival_comments'];
                $shipping_arrival_time_slot = $cart['shipping_arrival_time_slot'];
                $shipping_delivery_date = $cart['shipping_delivery_date'];
                if (isset($desiredArrivalDate) && !empty($desiredArrivalDate)){
                    $order->setData('shipping_arrival_date',$desiredArrivalDate);
                    $order->setData('shipping_arrival_comments',$shipping_arrival_comments);
                    $order->setData('shipping_arrival_time_slot',$shipping_arrival_time_slot);
                    $order->setData('shipping_delivery_date',$shipping_delivery_date);
                }
            
        }
        public function saveShippingArrivalDateAdmin($observer){
//
//            $order = $observer->getEvent()->getOrder();
//            $cart = Mage::app()->getRequest()->getParams();
//            $desiredArrivalDate = Mage::helper('deliverydate')->getFormatedDeliveryDateToSave($cart['shipping_arrival_date_display']);
//            $shipping_arrival_comments = $cart['shipping_arrival_comments'];
//            if (isset($desiredArrivalDate) && !empty($desiredArrivalDate)){
//                $order->setShippingArrivalComments($shipping_arrival_comments);
//                $order->setShippingArrivalDate($desiredArrivalDate);
//            }

        }

        public function deliverytimegetdata()
        {
            $collection = Mage::getModel('deliverydate/deliverydate')->getCollection()->setOrder('id','asc');
            return $collection;
        }

}