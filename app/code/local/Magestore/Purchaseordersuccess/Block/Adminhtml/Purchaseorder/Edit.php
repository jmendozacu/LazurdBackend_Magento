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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'purchase_order_id';
        $this->_controller = 'adminhtml_purchaseorder';
        $this->_blockGroup = 'purchaseordersuccess';

        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_removeButton('reset');

        $this->purchaseOrder = Mage::registry('current_purchase_order');
        $status = $this->purchaseOrder->getStatus();
        $purchaseId = $this->purchaseOrder->getPurchaseOrderId();
        $purchaseType = $this->purchaseOrder->getType();
        if ($this->purchaseOrder->getPurchaseOrderId()) {

            $printUrl = $this->getUrl('*/*/print', array('id' => $purchaseId, 'type' => $purchaseType));
            $this->_addButton('print', array(
                'label' => $this->__('Print'),
                'onclick' => sprintf(
                    "window.open('%s', 'PrintWindow', 'width=500,height=500,top=200,left=200').print()", $printUrl
                ),
                'class' => 'print',
            ), -1);

            if ($status != PurchaseorderStatus::STATUS_CANCELED) {
                $sendEmailLabel = 'Send Email';
                if ($status == PurchaseorderStatus::STATUS_PENDING)
                    $sendEmailLabel = 'Send Request';
                $sendEmailUrl = $this->getUrl('*/*/sendrequest', array('id' => $purchaseId, 'type' => $purchaseType));
                $this->_addButton('send_email', array(
                    'label' => $this->__($sendEmailLabel),
                    'onclick' => sprintf(
                        "confirmSetLocation('%s', '%s')", $this->__('Are you sure you want to do it?'), $sendEmailUrl
                    ),
                    'class' => 'send',
                ), -1);

                $this->_addButton('cancel', array(
                    'label' => $this->__('Cancel'),
                    'onclick' => "if(confirm('" . $this->__('Are you sure you want to do this?') . "')){
                            editForm.submit($('edit_form').action+'action_type/cancel/')
                        }",
                    'class' => 'delete',
                ), -1);

                if ($this->purchaseOrder->getType() == PurchaseorderType::TYPE_QUOTATION) {
                    $this->_addButton('convert', array(
                        'label' => $this->__('Convert Quotation to PO'),
                        'onclick' => "if(confirm('" . $this->__('Are you sure you want to convert this Quotation to Purchase Order?') . "')){
                            editForm.submit($('edit_form').action+'action_type/convert/')
                        }",
                        'class' => 'save',
                    ), -1);
                } else {
                    if ($status == PurchaseorderStatus::STATUS_PENDING) {
                        $this->_addButton('confirm', array(
                            'label' => $this->__('Confirm Purchase Order'),
                            'onclick' => "if(confirm('" . $this->__('Are you sure you want to make this Purchase Order?') . "')){
                            editForm.submit($('edit_form').action+'action_type/confirm/')
                        }",
                            'class' => 'save',
                        ), -1);
                    }

                    if ($this->purchaseOrder->canReceiveItem()) {
                        $this->_addButton('receive_item', array(
                            'label' => $this->__('Receive Items'),
                            'onclick' => "receiveItem.gridJsObject.reload()",
                            'class' => 'save',
                            'id' => 'receive_item_button_top',
                        ), 1);
                    }

                    if ($status == PurchaseorderStatus::STATUS_PROCESSING || $status == PurchaseorderStatus::STATUS_COMPLETED) {
                        $this->_addButton('transfer_item', array(
                            'label' => $this->__('Transfer Items'),
                            'onclick' => "transferItem.gridJsObject.reload()",
                            'class' => 'save',
                            'id' => 'transfer_item_button_top',
                        ), 1);
                    }

                    if ($status == PurchaseorderStatus::STATUS_PROCESSING) {
                        $this->_addButton('confirm', array(
                            'label' => $this->__('Complete PO'),
                            'onclick' => "if(confirm('" . $this->__('Are you sure you want to complete this Purchase Order?') . "')){
                            editForm.submit($('edit_form').action+'action_type/complete/')
                        }",
                            'class' => 'save',
                        ), 1);
                    }
                }
            } else {
                $this->_removeButton('save');
            }
        } else {
            $this->_updateButton('save', 'label', $this->__('Prepare Product List'));
        }

        if (!$this->purchaseOrder->getPurchaseOrderId()) {
            $baseCurrencyCode = Mage::app()->getLocale()->getCurrency();
            $this->_formScripts[] = "
                changeCurrency = function(){
                    $('note_currency_rate').innerHTML = '<span>(1 {$baseCurrencyCode} = ' 
                        + $('currency_rate').value + ' ' +$('currency_code').value + ')</span>';
                }
                window.onload = function (){
                     Event.observe($('currency_rate'), 'change', function(event){
                        if(event.target.value == '' || event.target.value == '0' || isNaN(event.target.value)) 
                        event.target.value = '1';
                    });
                    Event.observe($('currency_code'), 'change', function(){
                        changeCurrency();
                    });
                    Event.observe($('currency_rate'), 'change', function(){
                        changeCurrency();
                    });
                    $('currency_code').dispatchEvent(new Event('change'));
                    
                }
            ";
        }else{
            $baseCurrencyCode = $this->purchaseOrder->getCurrencyCode();
            $this->_formScripts[] = "
                window.onload = function (){
                     $('currency_code').value = '{$baseCurrencyCode}'
                }
            ";
        }
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        $type = $this->purchaseOrder->getPurchaseOrderId() ?
            $this->purchaseOrder->getType() :
            $this->getRequest()->getParam(
                Magestore_Purchaseordersuccess_Model_Purchaseorder::TYPE,
                Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_PURCHASE_ORDER
            );
        $typeLabel = Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::getTypeLabel($type);
        if ($this->purchaseOrder->getPurchaseOrderId()) {
            $code = $this->purchaseOrder->getPurchaseCode();
            $code = $code ? $code : $this->purchaseOrder->getId();
            return $this->__('View %s #%s', $typeLabel, $code);
        } else {
            return $this->__('New %s ', $typeLabel);
        }
    }


    /**
     * get html to show on header
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        echo $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_purchaseorder_timeline')
            ->setTemplate('purchaseordersuccess/timeline/step.phtml')->toHtml();
    }
}