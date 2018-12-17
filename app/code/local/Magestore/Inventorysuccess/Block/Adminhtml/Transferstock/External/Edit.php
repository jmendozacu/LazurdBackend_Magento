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

/**
 * Inventorysuccess Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'inventorysuccess';
        $this->_controller = 'adminhtml_transferstock_external';
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_removeButton('back');
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::registry('external_data');
        if (!$model->getId()) {
            $this->_removeButton('save');
            $this->_addButton('prepare_product_list', array(
                'label' => Mage::helper('adminhtml')->__('Prepare Product List'),
                'onclick' => 'saveGeneral()',
                'class' => 'btn-next-step',
            ), -100);
        } elseif ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING
            || $model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING
        ) {
            $this->_addButton('start_send_stock', array(
                'label' => Mage::helper('adminhtml')->__('Start Transferring'),
                'onclick' => 'startTransfer()',
                'class' => 'btn-start-transfer',
            ), -100);
        } else {
            $this->_removeButton('save');
        }
        $this->_formScripts[]
            = "
            function saveGeneral(){
                editForm.submit($('edit_form').action+'back/edit/step/save_general');
            }
            function startTransfer(){
                editForm.submit($('edit_form').action+'back/edit/step/start_transfer');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::registry('external_data');
        if ($model && $model->getId()) {
            if ($model->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL) {
                return Mage::helper('inventorysuccess')->__('Transfer to External location #%s (%s)',
                    $model->getData('transferstock_code'), $model->getData('status'));
            } else {
                return Mage::helper('inventorysuccess')->__('Transfer from External location #%s (%s)',
                    $model->getData('transferstock_code'), $model->getData('status'));
            }
        } else {
            if ($this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL) {
                return Mage::helper('inventorysuccess')->__('New Transfer to External Location');
            } else {
                return Mage::helper('inventorysuccess')->__('New Transfer from External Location');
            }
        }
    }
}
