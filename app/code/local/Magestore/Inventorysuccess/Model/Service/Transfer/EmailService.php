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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Inventorysuccess_Model_Service_Transfer_EmailService extends Varien_Object
{

    const EMAIL_TEMPLATE_TRANSFERSTOCK_CREATE           = 'transferstock_create';
    const EMAIL_TEMPLATE_TRANSFERSTOCK_DELIVERY         = 'transferstock_delivery';
    const EMAIL_TEMPLATE_TRANSFERSTOCK_RECEIVING        = 'transferstock_receiving';
    const EMAIL_TEMPLATE_TRANSFERSTOCK_DIRECTY_TRANSFER = 'transferstock_direct_transfer';

    /**
     * @param $transfer
     */
    protected function _init($transfer)
    {
        $this->setReceivers($transfer->getNotifierEmails());
        $templateVars                       = array();
        $templateVars['transferstock_id']   = $transfer->getId();
        $templateVars['transferstock_code'] = $transfer->getTransferstockCode();
        $templateVars['total_items']        = $transfer->getQty();
        $templateVars['created_by']         = $transfer->getCreatedBy();
        switch ($transfer->getType()) {
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_REQUEST:
                $templateVars['transfer_link'] = Mage::getUrl("adminhtml/inventorysuccess_transferstock_request/edit/id/" . $transfer->getId());
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND:
                $templateVars['transfer_link'] = Mage::getUrl("adminhtml/inventorysuccess_transferstock_send/edit/id/" . $transfer->getId());
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL:
                $templateVars['transfer_link'] = Mage::getUrl("adminhtml/inventorysuccess_transferstock_external/type/to_external/edit/id/" . $transfer->getId());
                break;
            case Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL:
                $templateVars['transfer_link'] = Mage::getUrl("adminhtml/inventorysuccess_transferstock_external/type/from_external/edit/id/" . $transfer->getId());
                break;
        }
        $this->setTemplateVars($templateVars);
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     */
    public function notifyCreateNewTransfer(Magestore_Inventorysuccess_Model_Transferstock $transfer)
    {
        $this->_init($transfer);
        $this->setTemplateId(self::EMAIL_TEMPLATE_TRANSFERSTOCK_CREATE);
        $this->sendEmail();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     */
    public function notifyCreateDirectTransfer(Magestore_Inventorysuccess_Model_Transferstock $transfer)
    {
        $this->_init($transfer);
        $this->setTemplateId(self::EMAIL_TEMPLATE_TRANSFERSTOCK_CREATE);
        $this->sendEmail();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     */
    public function notifyCreateReceiving(Magestore_Inventorysuccess_Model_Transferstock $transfer)
    {
        $this->_init($transfer);
        $this->setTemplateId(self::EMAIL_TEMPLATE_TRANSFERSTOCK_CREATE);
        $this->sendEmail();
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Transferstock $transfer
     */
    public function notifyCreateDelivery(Magestore_Inventorysuccess_Model_Transferstock $transfer)
    {
        $this->_init($transfer);
        $this->setTemplateId(self::EMAIL_TEMPLATE_TRANSFERSTOCK_CREATE);
        $this->sendEmail();
    }

    /**
     * @return $this
     */
    public function sendEmail()
    {
        $store     = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        foreach ($this->getReceivers() as $receiver) {
            /** @var Mage_Core_Model_Email_Template $emailTemplate */
            $emailTemplate = Mage::getModel('core/email_template');
            $emailTemplate->setDesignConfig(array(
                'area' => 'adminhtml',
                'store' => $store->getId()
            ));
            $emailTemplate->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $receiver,
                'Store owner',
                $this->getTemplateVars()
            );
        }

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * get array of receivers
     * @return array
     */
    protected function getReceivers()
    {
        $receivers = $this->getData('receivers');
        if (!is_array($receivers)) {
            $receivers = explode(',', $receivers);
        }
        return $receivers;
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return 'general';
    }
}