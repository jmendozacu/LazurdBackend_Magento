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

class Magestore_Webpos_Block_Payment_Method_Cash_Info_Cash extends Mage_Payment_Block_Info
{
	/* 
		This block will show the payment method information
	*/
	
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }
	protected function _construct() 
    {
        parent::_construct();
        /* 
			$this->setTemplate('webpos/admin/webpos/payment/method/info/cash.phtml'); 
		*/
    }
	
	public function getMethodTitle(){
		return Mage::helper('webpos/payment')->getCashMethodTitle();
	}

}
