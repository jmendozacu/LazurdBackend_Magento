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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Sharecredit_Validatecustomer extends Mage_Core_Block_Template
{

    /**
     * prepare block's layout
     *
     * @return Magestore_Customercredit_Block_Customercredit
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getVerifyEnable()
    {
        return Mage::helper('customercredit')->getGeneralConfig('validate');
    }

    /**
     * @return mixed
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('customercredit/index/sharepost');
    }

    /**
     * @return mixed
     */
    public function getVerifyCode()
    {
        $code = $this->getRequest()->getParam('keycode');
        if ($code) {
            return $code;
        }
    }

}
