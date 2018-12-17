<?php
// Islam Elgarhy PDF 2018
class Potato_Pdf_Helper_Sales extends Mage_Core_Helper_Abstract
{
    public function getOrderVariables($order)
    {
        $this->_register('current_order', $order);
        $store = Mage::getModel('core/store')->load($order->getData('store_id'));
        $sname = $store->getName();
       
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        return array(
            'store_id'     => $order->getStoreId(),
            'order'        => $order,
            'customer'     => $customer,
            'comments'     => $order->getStatusHistoryCollection(true),
            'order_items'  => $this->getAllVisibleItems($order),
            'payment_html' => $this->getPaymentInfoFromOrder($order),
            'store_nameonly'                  => $sname
        );
    } 

    public function getInvoiceVariables($invoice)
    {
        
        $order = $invoice->getOrder();
        $this
            ->_register('current_order', $order)
            ->_register('current_invoice', $invoice)
        ;
 
       $store = Mage::getModel('core/store')->load($order->getData('store_id'));
       $sname = $store->getName();
       
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        return array(
            'store_id'               => $order->getStoreId(),
            'invoice'                => $invoice,
            'order'                  => $order,
            'invoice_items'          => $this->getAllVisibleItems($invoice),
            'customer'               => $customer,
            'comments'               => $order->getStatusHistoryCollection(true),
            'invoice_formatted_date' => $this->getCreatedAtFormated('full', $invoice->getCreatedAt()),
            'payment_html'           => $this->getPaymentInfoFromOrder($order),
            'store_nameonly'                  => $sname
        );
        /*
        $order = $invoice;
        $store = Mage::getModel('core/store')->load($order->getData('store_id'));
        $sname = $store->getName();
        
         $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
         return array(
             'store_id'               => $order->getStoreId(),
             'invoice'                => $invoice,
             'order'                  => $order,
             'invoice_items'          => $this->getAllVisibleItems($invoice),
             'customer'               => $customer,
             'comments'               => $order->getStatusHistoryCollection(true),
             'invoice_formatted_date' => $this->getCreatedAtFormated('full', $invoice->getCreatedAt()),
             'payment_html'           => $this->getPaymentInfoFromOrder($order),
             'store_nameonly'                  => $sname
         );
         */
    }

    public function getAllVisibleItems($object)
    {
        if ($object instanceof Mage_Sales_Model_Order)
        {
            return $object->getAllVisibleItems();
        }
        $items = array();
        foreach ($object->getAllItems() as $item) {
            if (!$item->getOrderItem() || $item->getOrderItem()->getParentItem()) {
                continue;
            }
            array_push($items, $item);
        }
        return $items;
    }

    public function getShipmentVariables($shipment)
    {
        $order = $shipment->getOrder();
        $this
            ->_register('current_order', $order)
            ->_register('current_shipment', $shipment)
        ;
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        return array(
            'store_id'                => $order->getStoreId(),
            'shipment'                => $shipment,
            'order'                   => $order,
            'shipment_items'          => $this->getAllVisibleItems($shipment),
            'customer'                => $customer,
            'comments'                => $order->getStatusHistoryCollection(true),
            'shipment_formatted_date' => $this->getCreatedAtFormated('long', $shipment->getCreatedAt()),
            'payment_html'            => $this->getPaymentInfoFromOrder($order)
        );
    }

    public function getCreditMemoVariables($creditMemo)
    {
        $order = $creditMemo->getOrder();
        $this
            ->_register('current_order', $order)
            ->_register('current_creditmemo', $creditMemo)
        ;
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        return array(
            'store_id'                  => $order->getStoreId(),
            'creditmemo'                => $creditMemo,
            'order'                     => $order,
            'creditmemo_items'          => $this->getAllVisibleItems($creditMemo),
            'customer'                  => $customer,
            'comments'                  => $order->getStatusHistoryCollection(true),
            'creditmemo_formatted_date' => $this->getCreatedAtFormated('long', $creditMemo->getCreatedAt()),
            'payment_html'              => $this->getPaymentInfoFromOrder($order)
        );
    }

    /**
     * Get formated created date
     *
     * @param   string $format date format type (short|medium|long|full)
     * @param   string $date
     * @return  string
     */
    public function getCreatedAtFormated($format, $date)
    {
        return Mage::helper('core')->formatDate($date, $format, true);
    }

    public function getPaymentInfoFromOrder($order)
    {
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($order->getStoreId());
        return $paymentBlock->toHtml();
    }

    protected function _register($name, $value)
    {
        Mage::unregister($name);
        Mage::register($name, $value);
        return $this;
    }
}