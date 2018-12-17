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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_SendstockController
    extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'inventorysuccess/view_transferstock/view_sendstock';
        switch ( $this->getRequest()->getActionName() ) {
            case 'new' :
            case 'save':
                $resource = 'inventorysuccess/create_transferstock/create_sendstock';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('inventorysuccess/view_transferstock/view_sendstock');
        return $this->renderLayout();
    }

    /**
     * render ajax view
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     *
     */
    public function newAction()
    {
        $this->renderForm();
    }

    /**
     *
     */
    public function editAction()
    {
        $this->renderForm();
    }

    protected function renderForm()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::getModel('inventorysuccess/transferstock')->load($id);
        if ( $model->getId() || !$id ) {
            $data = $this->_getSession()->getFormData(true);
            if ( !empty($data) ) {
                $model->setData($data);
            }
            if ( !$id ) {
                $model->setData('transferstock_code',
                                Magestore_Coresuccess_Model_Service::incrementIdService()->getNextCode(Magestore_Inventorysuccess_Model_Transferstock::TRANSFER_CODE_PREFIX));
            }
            Mage::register('sendstock_data', $model);
            $this->_initEditForm();
            if ( $model->getId() ) {
                $this->_title($this->__('Send Stock #%s', $model->getData('transferstock_code')));
            } else {
                $this->_title($this->__('New Stock Sending'));
            }
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(
                Mage::helper('inventorysuccess')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * @return $this
     */
    protected function _initEditForm()
    {
        $this->loadLayout();
        $this->_setActiveMenu('inventorysuccess/create_transferstock/create_sendstock');
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Send Stock'),
            Mage::helper('adminhtml')->__('Send Stock')
        );
        $this->_title($this->__('InventorySuccess'))
             ->_title($this->__('Send Stock'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_edit'))
             ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_edit_tabs'));
        return $this;
    }

    /**
     *
     */
    public function saveAction()
    {
        $step = $this->getRequest()->getParam('step');
        switch ( $step ) {
            case "save_general" :
                $this->_saveGeneral();
                break;
            case "start_send":
                $this->_startSend();
                break;
            case "direct_transfer":
                $this->_directTransfer();
                break;
            case "save_receiving":
                $this->_saveReceiving();
                break;
            case "complete":
                $this->_complete();
                break;
            default:
                $this->_save();
        }
    }

    /**
     * Process step 1
     */
    protected function _saveGeneral()
    {
        if ( $data = $this->getRequest()->getPost() ) {
            /** Validate Data */
            $validateResult = $this->_getService()->validateTranferGeneralForm($data);
            if ( !$validateResult['is_validate'] ) {
                foreach ( $validateResult['errors'] as $error ) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => null));
                return $this;
            }
            /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
            $model        = Mage::getModel('inventorysuccess/transferstock');
            $data['type'] = Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND;
            $this->_getService()->initTransfer($model, $data);
            try {
                $model->getResource()->save($model);
                $this->_getSession()->setFormData(false);
                $this->_getSession()->setData('send_products', false);
                $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('General information has been saved successfully.'));
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return $this;
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => null));
                return $this;
            }
        }
        $this->_getSession()->addError(
            Mage::helper('inventorysuccess')->__('Unable to find item to save')
        );
        return $this;
    }

    /**
     *
     */
    protected function _save()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducs();
        /** save general information first */
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::NOTIFIER_EMAILS, $this->getRequest()->getParam('notifier_emails'));
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::REASON, $this->getRequest()->getParam('reason'));
        $transfer->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('General information has been saved successfully.'));
        /** save products */
        if ( !count($products) ) {
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getSession()->setData('send_products', $products);
        try {
            if ( $this->_getService()->validateStockDelivery($transfer, $products) ) {
                $this->_getService()->setProducts($transfer, $products);
                $this->_getSession()->setData('send_products', null);
                $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer has been successfully saved.'));
            }
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_getSession()->setFormData(false);
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     *
     */
    protected function _startSend()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducs();
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to send."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getSession()->setData('send_products', $products);
        if ( $this->_getService()->saveTransferStockProduct($transfer, $products, true, false) ) {
            $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING);
            $transfer->save();
            /** message */
            $this->_getSession()->setData('send_products', null);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Sent ' . count($products) . ' product(s) to the destination warehouse!'));
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     *
     */
    protected function _directTransfer()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducs();
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to send."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getSession()->setData('send_products', $products);
        if ( $this->_getService()->saveTransferStockProduct($transfer, $products, false, true) ) {
            $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED);
            $transfer->save();
            /** message */
            $this->_getSession()->setData('send_products', null);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Sent ' . count($products) . ' product(s) directly to the destination warehouse!'));
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     *
     */
    protected function _saveReceiving()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducs();
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to send."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getSession()->setData('send_receiving_products', $products);
        $refinedProducts = array();
        foreach ( $products as $product ) {
            $product['qty'] = $product['new_qty'];
            unset($product['new_qty']);
            $refinedProducts[$product['product_id']] = $product;
        }
        try {
            $this->_getService()->saveTransferActivityProduct($transfer, $refinedProducts,
                                                              Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING);
            $transfer->save();
            /** message */
            $this->_getSession()->setData('send_receiving_products', null);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Receiving has been created successfully.'));
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     * Mark as complete
     * @return $this
     */
    protected function _complete()
    {
        $transfer = $this->_getCurrentTransfer();
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED);
        $transfer->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Marked stock sending #%s as complete.', $transfer->getTransferstockCode()));
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     *
     */
    public function exportShortfallAction()
    {
        $transferId = $this->getRequest()->getParam('id');
        $fileName   = 'shortfall_list_' . $transferId . '.csv';
        $content    = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_edit_tab_stocksummary')
                           ->setData('is_shortfall', true)->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportSummaryAction()
    {
        $transferId = $this->getRequest()->getParam('id');
        $fileName   = 'stock_summary_' . $transferId . '.csv';
        $content    = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_edit_tab_stocksummary')
                           ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName = 'send_stock.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    protected function _getCurrentTransfer()
    {
        $transferId = $this->getRequest()->getParam('id');
        $transfer   = Mage::getModel('inventorysuccess/transferstock')->load($transferId);
        return $transfer;
    }

    /**
     * @return array
     */
    protected function _getSelectedProducs()
    {
        $products = $this->getRequest()->getParam('products');
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($products);
        return $products;
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    protected function _getService()
    {
        return Magestore_Coresuccess_Model_Service::transferStockService();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('sendstock.productlist')
             ->setProductsSelected($this->getRequest()->getPost('products_selected', null));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistgridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('sendstock.productlist')
             ->setProductsSelected($this->getRequest()->getPost('products_selected', null));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function stocksummaryAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function receivingAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function receivinggridAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function receivinghistorygridAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     *
     */
    public function downloadsampleAction()
    {
        $fileName = 'import_product_to_send.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_sample')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function downloadsampleReceivingAction()
    {
        $fileName = 'import_product_to_receive.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_sample')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function importAction()
    {
        $transfer = $this->_getCurrentTransfer();
        if ( $this->getRequest()->isPost() ) {
            try {
                $importHandler = Magestore_Coresuccess_Model_Service::transferImportService();
                $data          = $importHandler->importFromCsvFile(
                    $_FILES['import-send'],
                    Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND);
                if ( !$this->_getService()->saveTransferStockProduct($transfer, $data, false, false) ) {
                    $this->_getSession()->setData("send_products", null);
                } else {
                    $this->_getSession()->addSuccess($this->__('The product transfer has been imported.'));
                }
            } catch
            ( \Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
    }

    /**
     *
     */
    public function importReceivingAction()
    {
        $transfer = $this->_getCurrentTransfer();
        if ( $this->getRequest()->isPost() ) {
            try {
                $importHandler = Magestore_Coresuccess_Model_Service::transferImportService();
                $data          = $importHandler->importFromCsvFile(
                    $_FILES['import-send-receiving'],
                    Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_SEND_RECEIVING);
                $this->_getService()->saveTransferActivityProduct($transfer, $data,
                                                                  Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING);
                $this->_getSession()->addSuccess($this->__('The product receiving has been imported.'));
            } catch
            ( \Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
    }

    /**
     *
     */
    public function downloadInvalidAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_SEND;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName));
    }

    /**
     *
     */
    public function downloadInvalidReceivingAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_SEND_RECEIVING;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName));
    }


    /**
     * handle after scan barcode
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function handleBarcodeAction()
    {
        $barcodes        = $this->_getSession()->getData('scan_barcodes', true);
        $transferstockId = $this->getRequest()->getParam('transferstock_id');
        if ( $barcodes ) {
            $transferStock = Mage::getModel('inventorysuccess/transferstock')->load($transferstockId);
            Magestore_Coresuccess_Model_Service::transferStockService()->addProductFromBarcode($transferStock, $barcodes);
        }
        return $this->_redirect('*/*/edit', array('id' => $transferstockId));
    }

    /**
     * handle after scan barcode in receving tab
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function handleBarcodeReceivingAction()
    {
        $barcodes        = $this->_getSession()->getData('scan_barcodes', true);
        $transferstockId = $this->getRequest()->getParam('transferstock_id');
        if ( $barcodes ) {
            $transferStock = Mage::getModel('inventorysuccess/transferstock')->load($transferstockId);
            Magestore_Coresuccess_Model_Service::transferStockService()->createTransferActivityFromBarcode($transferStock, $barcodes, Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING);

        }
        return $this->_redirect('*/*/edit', array('id' => $transferstockId));
    }

}
