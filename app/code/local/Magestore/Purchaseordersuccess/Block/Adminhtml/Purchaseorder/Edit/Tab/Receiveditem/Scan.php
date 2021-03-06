<?php

/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess3
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Receiveditem_Scan
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Barcode_Scan
{
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/received_item/scan.phtml';

    /**
     * @var array
     */
    protected $reloadTabs = array(
        'purchase_order_tabs_received_item',
        'purchase_order_tabs_returned_item',
        'purchase_order_tabs_transferred_item'
    );
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->purchaseOrder->canReceiveItem())
            return parent::_toHtml();
        else
            return '';
    }
    
    /**
     * Add Received Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addScanBarcodeField()
    {
        $html = $this->addField('scan_barcode_received_at',
            'date',
            array(
                'name' => 'received_at',
                'time' => false,
                'label' => $this->__('Received Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class'     => 'validate-date',
                'min_date' => $this->purchaseOrder->getPurchasedAt(),
                'value'     => new Zend_Date($this->purchaseOrder->getPurchasedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        $html .= $this->addField('barcode-receive-item',
            'text',
            array(
                'name' => 'barcode',
                'label' => $this->__('Barcode'),
            )
        );
        return $html;
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Receiveditem_Receiveitem_Form');
        return $this->form;
    }

    public function getLoadBarcodeUrl()
    {
        return $this->getUrl(
            'adminhtml/purchaseordersuccess_purchaseorder_receiveditem/loadBarcode',
            array(
                'id' => $this->purchaseOrder->getPurchaseOrderId(),
                'supplier_id' => $this->purchaseOrder->getSupplierId()
            )
        );
    }

    public function getSubmitBarcodeUrl()
    {
        return $this->getUrl(
            'adminhtml/purchaseordersuccess_purchaseorder_receiveditem/receive',
            array(
                'id' => $this->purchaseOrder->getPurchaseOrderId(),
                'supplier_id' => $this->purchaseOrder->getSupplierId(),
                'modal' => 'scanbarcode'
            )
        );
    }
}