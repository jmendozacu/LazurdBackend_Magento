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
 * Class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_RequeststockController
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_RequeststockController
    extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'inventorysuccess/view_transferstock/view_requeststock';
        switch ( $this->getRequest()->getActionName() ) {
            case 'new' :
            case 'save':
                $resource = 'inventorysuccess/create_transferstock/create_requeststock';
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
        $this->_setActiveMenu('inventorysuccess/view_transferstock/view_requeststock');
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
            Mage::register('requeststock_data', $model);
            $this->_initEditForm();
            if ( $model->getId() ) {
                $this->_title($this->__('Request Stock #%s', $model->getData('transferstock_code')));
            } else {
                $this->_title($this->__('New Stock Request'));
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
        $this->_setActiveMenu('inventorysuccess/create_transferstock/create_requeststock');
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Request Stock'),
            Mage::helper('adminhtml')->__('Request Stock')
        );
        $this->_title($this->__('InventorySuccess'))
             ->_title($this->__('Request Stock'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_edit'))
             ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_edit_tabs'));
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
            case "start_request":
                $this->_startRequest();
                break;
            case "complete":
                $this->_complete();
                break;
            case "save_delivery":
                $this->_saveDelivery();
                break;
            case "save_receiving":
                $this->_saveReceiving();
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
            $data['type'] = Magestore_Inventorysuccess_Model_Transferstock::TYPE_REQUEST;
            $this->_getService()->initTransfer($model, $data);
            try {
                $model->getResource()->save($model);
                $this->_getSession()->setFormData(false);
                $this->_getSession()->setData('request_products', false);
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
     * @return $this
     */
    protected function _save()
    {
        $transfer    = $this->_getCurrentTransfer();
        $productlist = $this->_getSelectedProducts();
        /** save general information first */
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::NOTIFIER_EMAILS, $this->getRequest()->getParam('notifier_emails'));
        $transfer->setData(Magestore_Inventorysuccess_Model_Transferstock::REASON, $this->getRequest()->getParam('reason'));
        $transfer->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('General information has been saved successfully.'));
        /** save products */
        if ( !count($productlist) ) {
//            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to request."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        try {
            $this->_getService()->setProducts($transfer, $productlist);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Stock transfer has been successfully saved.'));
        } catch ( \Exception $e ) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_getSession()->setFormData(false);
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     * // -> change status to procesing
     * // -> saved product
     * // -> send email notification
     * @return $this
     */
    protected function _startRequest()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->_getSelectedProducts();
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to request."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getService()->saveTransferStockProduct($transfer, $products);
        $transfer->setStatus(Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING);
        $transfer->save();
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Request stock #' . $transfer->getTransferstockCode() . ' is ready to deliver and receive!'));
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     * @return $this
     */
    protected function _saveDelivery()
    {
        $transfer = $this->_getCurrentTransfer();
        $products = $this->getRequest()->getParam('products');
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($products['delivery']);
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to deliver."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }
        $this->_getSession()->setData('request_delivery_products', $products);

        /** refine products array('product_id'=>[]) */
        $refinedProducts = array();
        foreach ( $products as $product ) {
            $product['qty'] = $product['new_qty'];
            unset($product['new_qty']);
            $refinedProducts[$product['product_id']] = $product;
        }

        $isValid = $this->_getService()->validateStockDelivery($transfer, $refinedProducts);
        if ( $isValid ) {
            $this->_getService()->saveTransferActivityProduct($transfer, $refinedProducts,
                                                              Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY);
            $this->_getSession()->setData('request_delivery_products', null);
            $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Create delivery successfully!'));
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
        $products = $this->getRequest()->getParam('products');
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($products['receiving']);
        if ( !count($products) ) {
            $this->_getSession()->addError(Mage::helper('inventorysuccess')->__("There is no product to receive."));
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return $this;
        }

        /** Refine products array('product_id' => []) */
        $refinedProducts = array();
        foreach ( $products as $product ) {
            $product['qty'] = $product['new_qty'];
            unset($product['new_qty']);
            $refinedProducts[$product['product_id']] = $product;
        }
        $this->_getService()->saveTransferActivityProduct($transfer, $refinedProducts,
                                                          Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_RECEIVING);
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Receiving has been created successfully.'));
        $this->_redirect('*/*/edit', array('id' => $transfer->getId(), 'activeTab' => 'receiving_history'));
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
        $this->_getSession()->addSuccess(Mage::helper('inventorysuccess')->__('Marked stock request #%s as completed', $transfer->getTransferstockCode()));
        $this->_redirect('*/*/edit', array('id' => $transfer->getId()));
        return $this;
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getParam('products');
        $products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($products);
        return $products;
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
     * @return Magestore_Inventorysuccess_Model_Service_Transfer_TransferService
     */
    protected function _getService()
    {
        return Magestore_Coresuccess_Model_Service::transferStockService();
    }

    /**
     *
     */
    public function exportShortfallAction()
    {

        $transferId = $this->getRequest()->getParam('id');
        $fileName   = 'shortfall_list_' . $transferId . '.csv';
        $content    = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_edit_tab_stocksummary')
                           ->setData('is_shortfall', true)->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportSummaryAction()
    {
        $fileName = 'stock_summary.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_edit_tab_stocksummary')
                         ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function exportCsvAction()
    {
        $fileName = 'request_stock.csv';
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('requeststock.productlist')
             ->setProductsSelected($this->getRequest()->getPost('products_selected', null));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productlistgridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('requeststock.productlist')
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
    public function deliveryAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function deliverygridAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function deliveryhistorygridAction()
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
                         ->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_sample')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    public function downloadsampleDeliveryAction()
    {
        $fileName = 'import_product_to_delivery.csv';
        $content  = $this->getLayout()
                         ->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_sample')
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
                         ->createBlock('inventorysuccess/adminhtml_transferstock_requeststock_sample')
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
                $data          = $importHandler->importFromCsvFile($_FILES['import-request'],
                                                                   Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST);
                if ( !$this->_getService()->saveTransferStockProduct($transfer, $data, false, false) ) {
//                    $this->_locatorFactory->create()->refreshSessionByKey("request_products");
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
    public function importDeliveryAction()
    {
        $transfer = $this->_getCurrentTransfer();
        if ( $this->getRequest()->isPost() ) {
            try {
                $importHandler = Magestore_Coresuccess_Model_Service::transferImportService();
                $data          = $importHandler->importFromCsvFile($_FILES['import-request-delivery'],
                                                                   Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_DELIVERY);
                $this->_getService()->saveTransferActivityProduct($transfer, $data,
                                                                  Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY);
                $this->_getSession()->addSuccess($this->__('The product delivery has been imported.'));
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
                $data          = $importHandler->importFromCsvFile($_FILES['import-request-receiving'],
                                                                   Magestore_Inventorysuccess_Model_ImportType::TYPE_TRANSFER_STOCK_REQUEST_RECEIVING);
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
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName));
    }

    /**
     *
     */
    public function downloadInvalidDeliveryAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST_DELIVERY;
        $this->_prepareDownloadResponse($fileName,
                                        file_get_contents(Mage::getBaseDir('media') . DS . 'inventorysuccess' . DS . $fileName));
    }

    /**
     *
     */
    public function downloadInvalidReceivingAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_TRANSFER_STOCK_REQUEST_RECEIVING;
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
     * handle after scan barcode in delivery tab
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function handleBarcodeDeliveryAction()
    {
        $barcodes        = $this->_getSession()->getData('scan_barcodes', true);
        $transferstockId = $this->getRequest()->getParam('transferstock_id');
        if ( $barcodes ) {
            $transferStock = Mage::getModel('inventorysuccess/transferstock')->load($transferstockId);
            Magestore_Coresuccess_Model_Service::transferStockService()->createTransferActivityFromBarcode($transferStock, $barcodes, Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY);
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
