<?php
//require('../app/code/core/Mage/Sales/Helper/Guest.php');
class Ewall_CustomConfig_Helper_Guest extends Mage_Sales_Helper_Guest{
	
    /**
     * Cookie params
     */
    protected $_cookieName  = 'guest-view';
    protected $_lifeTime    = 600;

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @return bool|null
     */
    public function loadValidOrder()
    {   /*echo $encrypted = Mage::helper('core')->urlEncode('100000004');
        echo Mage::helper('core')->urlDecode($encrypted);*/
        $post = Mage::app()->getRequest()->getPost();
        $order_id = Mage::helper('core')->urlDecode(Mage::app()->getRequest()->getParam('order_id'));
        $errors = false;
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order');
        /** @var Mage_Core_Model_Cookie $cookieModel */
        $cookieModel = Mage::getSingleton('core/cookie');
        $errorMessage = 'Entered data is incorrect. Please try again.';

        if (empty($post) && !$cookieModel->get($this->_cookieName)) {
            $order->loadByIncrementId($order_id);
            Mage::register('current_order', $order);

            return true;
        } elseif (!empty($post) && isset($post['oar_order_id']) && isset($post['oar_type']))  {
            $type           = $post['oar_type'];
            $incrementId    = $post['oar_order_id'];
            $lastName       = $post['oar_billing_lastname'];
            $email          = $post['oar_email'];
            $zip            = $post['oar_zip'];

            if (empty($incrementId) || empty($lastName) || empty($type) || (!in_array($type, array('email', 'zip')))
                || ($type == 'email' && empty($email)) || ($type == 'zip' && empty($zip))) {
                $errors = true;
            }

            if (!$errors) {
                $order->loadByIncrementId($incrementId);
            }

            if ($order->getId()) {
                $billingAddress = $order->getBillingAddress();
                if ((strtolower($lastName) != strtolower($billingAddress->getLastname()))
                    || ($type == 'email'
                        && strtolower($email) != strtolower($billingAddress->getEmail()))
                    || ($type == 'zip'
                        && (strtolower($zip) != strtolower($billingAddress->getPostcode())))
                ) {
                    $errors = true;
                }
            } else {
                $errors = true;
            }

            if ($errors === false && !is_null($order->getCustomerId())) {
                $errorMessage = 'Please log in to view your order details.';
                $errors = true;
            }

            if (!$errors) {
                $toCookie = base64_encode($order->getProtectCode() . ':' . $incrementId);
                $cookieModel->set($this->_cookieName, $toCookie, $this->_lifeTime, '/');
            }
        } elseif ($cookieModel->get($this->_cookieName)) {
            $cookie = $cookieModel->get($this->_cookieName);
            $cookieOrder = $this->_loadOrderByCookie( $cookie );
            if( !is_null( $cookieOrder) ){
                if( is_null( $cookieOrder->getCustomerId() ) ){
                    $cookieModel->renew($this->_cookieName, $this->_lifeTime, '/');
                    $order = $cookieOrder;
                } else {
                    $errorMessage = 'Please log in to view your order details.';
                    $errors = true;
                }
            } else {
                $errors = true;
            }
        }

        if (!$errors && $order->getId()) {
            Mage::register('current_order', $order);
            return true;
        }

        Mage::getSingleton('core/session')->addError($this->__($errorMessage));
        Mage::app()->getResponse()->setRedirect(Mage::getUrl('sales/guest/form'));
        return false;
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param  Mage_Core_Controller_Front_Action $controller
     */
    public function getBreadcrumbs($controller)
    {
        
    }

    /**
     * Try to load order by cookie hash
     * 
     * @param string|null $cookie
     * @return null|Mage_Sales_Model_Order
     */
    protected function _loadOrderByCookie($cookie = null)
    {
        if (!is_null($cookie)) {
            $cookieData = explode(':', base64_decode($cookie));
            $protectCode = isset($cookieData[0]) ? $cookieData[0] : null;
            $incrementId = isset($cookieData[1]) ? $cookieData[1] : null;

            if (!empty($protectCode) && !empty($incrementId)) {
                /** @var $order Mage_Sales_Model_Order */
                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($incrementId);

                if ($order->getProtectCode() === $protectCode) {
                    return $order;
                }
            }
        }
        return null;
    }

    /**
     * Getter for $this->_cookieName
     *
     * @return string
     */
    public function getCookieName()
    {
        return $this->_cookieName;
    }

}
?>