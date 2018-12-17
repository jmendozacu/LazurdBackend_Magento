<?php
    class Cmsmart_Deliverydate_Block_Order_Info extends Mage_Core_Block_Template
    {
        protected $_links = array();

        protected function _construct()
        {
            parent::_construct();
            if (Mage::getStoreConfig('deliverydate/deliverydate_general/enabled')){
                $this->setTemplate('cmsmart/deliverydate/order/info.phtml');
            }else{
                $this->setTemplate('sales/order/info.phtml');
            }
        }

        protected function _prepareLayout()
        {
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($this->__('Order # %s', $this->getOrder()->getRealOrderId()));
            }
            $this->setChild(
                'payment_info',
                $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
            );
        }

        public function getPaymentInfoHtml()
        {
            return $this->getChildHtml('payment_info');
        }
        public function getOrder()
        {
            return Mage::registry('current_order');
        }

        public function addLink($name, $path, $label)
        {
            $this->_links[$name] = new Varien_Object(array(
                    'name' => $name,
                    'label' => $label,
                    'url' => empty($path) ? '' : Mage::getUrl($path, array('order_id' => $this->getOrder()->getId()))
                ));
            return $this;
        }

        public function getLinks()
        {
            $this->checkLinks();
            return $this->_links;
        }

        private function checkLinks()
        {
            $order = $this->getOrder();
            if (!$order->hasInvoices()) {
                unset($this->_links['invoice']);
            }
            if (!$order->hasShipments()) {
                unset($this->_links['shipment']);
            }
            if (!$order->hasCreditmemos()) {
                unset($this->_links['creditmemo']);
            }
        }

        public function getReorderUrl($order)
        {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                return $this->getUrl('sales/guest/reorder', array('order_id' => $order->getId()));
            }
            return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
        }

        public function getPrintUrl($order)
        {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
            }
            return $this->getUrl('sales/order/print', array('order_id' => $order->getId()));
        }

    }
