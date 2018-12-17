<?php

class Cmsmart_Deliverydate_Model_Observer {
    
    public function checkout_controller_onepage_save_shipping_method($observer) {

        $request = $observer->getEvent()->getRequest();
        $quote = $observer->getEvent()->getQuote();
        $shiping = $request->getPost('shiping');
        Mage::getSingleton('core/session')->setMyShiping($shiping);
        if ($shiping == 1) {
            $desiredArrivalDate = $request->getPost('shipping_arrival_date');
            $comt = $request->getPost('shipping_arrival_comments');
            if (isset($desiredArrivalDate) && !empty($desiredArrivalDate)) {
                $quote->setData('shipping_arrival_date', $desiredArrivalDate);
                $quote->setData('shipping_arrival_comments', $comt);
                // $quote->setData('shipping_delivery_date', date('Y-m-d H:i:s',strtotime($desiredDate)));
                $quote->setData('shipping_delivery_date', Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s',$desiredDate));
                $quote->save();
            }
        }
        if ($shiping == 2) {
            $desiredDate = $request->getPost('shipping_arrival_date1');
            $time = $request->getPost('shipping_arrival_time');
            $slot = Mage::getModel('deliverydate/deliverydate')->load($time);
            $tim_slot = $slot->getData('fromtime')." - ".$slot->getData('totime');
            $desiredArrivalDate = $desiredDate . " " . $tim_slot;
            $delivery_date = date('Y-m-d ',strtotime($desiredDate))." ".$slot->getData('totime');
           // $desireddeliveryDate = date('Y-m-d H:i:s',strtotime($delivery_date));
            $desireddeliveryDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s',$delivery_date);
            $comt = $request->getPost('shipping_arrival_comments1');
            if (isset($desiredArrivalDate) && !empty($desiredArrivalDate)) {
                $quote->setData('shipping_arrival_date', $desiredArrivalDate);
                $quote->setData('shipping_arrival_comments', $comt);
                $quote->setData('shipping_arrival_time_slot', $time);
                $quote->setData('shipping_delivery_date', $desireddeliveryDate);
                $quote->save();
            }
        }
        return $this;
    }

    public function setcustommoney($observer) {
            $shipping_method= Mage::getSingleton('core/session')->getMyShiping();
            $quote = $observer->getEvent()->getQuote();
            $quoteid = $quote->getId();
            $custom = Mage::getStoreConfig('deliverydate/deliverydate_general/deliverydate_custom');
            if ($quoteid && $shipping_method == 1) {
                if ($quoteid > 0) {
                    $total = $quote->getBaseSubtotal();
                    $quote->setSubtotal(0);
                    $quote->setBaseSubtotal(0);

                    $quote->setSubtotalWithDiscount(0);
                    $quote->setBaseSubtotalWithDiscount(0);

                    $quote->setGrandTotal(0);
                    $quote->setBaseGrandTotal(0);


                    $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                    foreach ($quote->getAllAddresses() as $address) {

                        $address->setSubtotal(0);
                        $address->setBaseSubtotal(0);

                        $address->setGrandTotal(0);
                        $address->setBaseGrandTotal(0);

                        $address->collectTotals();

                        $quote->setGrandTotal($quote->getBaseSubtotal() + $custom)
                                ->setBaseGrandTotal($quote->getBaseSubtotal() + $custom)
                                ->setSubtotalWithDiscount($quote->getBaseSubtotal() + $custom)
                                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() + $custom)
                                ->save();


                        if ($address->getAddressType() == $canAddItems) {
                            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() + $custom);
                            $address->setGrandTotal((float) $address->getGrandTotal() + $custom);
                            $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() + $custom);
                            $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() + $custom);
                            if ($address->getDiscountDescription()) {
                                $address->setDiscountAmount(+($address->getDiscountAmount() + $custom));
                                $address->setDiscountDescription($address->getDiscountDescription() . ', Custom Money');
                                $address->setBaseDiscountAmount(+($address->getBaseDiscountAmount() + $custom));
                            } else {
                                $address->setDiscountAmount(+($custom));
                                $address->setDiscountDescription('Custom Money');
                                $address->setBaseDiscountAmount(+($custom));
                            }
                            $address->save();
                        }//end: if
                    } //end: foreach
                    //echo $quote->getGrandTotal();

                    foreach ($quote->getAllItems() as $item) {
                        //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                        $rat = $item->getPriceInclTax() / $total;
                        $ratdisc = $custom * $rat;
                        $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                        $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
                    }
                }
            }
        }
    

}
