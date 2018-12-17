<?php

class Potato_Pdf_Adminhtml_Potato_Pdf_TemplateController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/po_pdf');

        $this
            ->_title($this->__('Sales'))
            ->_title($this->__('PotatoCommerce - Print PDF'))
        ;
        return $this;
    }

    protected function _initTemplate()
    {
        $template = Mage::getModel('po_pdf/template');
        $templateId  = (int) $this->getRequest()->getParam('id', 0);
        if ($templateId) {
            try {
                $template->load($templateId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        if (null !== Mage::getSingleton('adminhtml/session')->getPDFStoreFormData()
            && is_array(Mage::getSingleton('adminhtml/session')->getPDFStoreFormData())
        ) {
            $template->addData(Mage::getSingleton('adminhtml/session')->getPDFStoreFormData());
            Mage::getSingleton('adminhtml/session')->setPDFStoreFormData(null);
        }
        Mage::unregister('current_template');
        Mage::register('current_template', $template);
        return $template;
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->_initAction();
        $this->_title($this->__('Templates'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $rule = $this->_initTemplate();
        $this->_initAction();
        $this->_title($this->__('Templates'));

        $breadcrumbTitle = $breadcrumbLabel = $this->__('New Template');
        if ($rule->getId()) {
            $breadcrumbTitle = $breadcrumbLabel = $this->__('Edit Template');
        }
        $this
            ->_title($breadcrumbTitle)
            ->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle)
            ->renderLayout()
        ;
    }

    public function saveAction()
    {
        if ($formData = $this->getRequest()->getPost()) {
            $template = $this->_initTemplate();
            try {
                $template
                    ->addData($formData)
                    ->save()
                ;
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Template successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPDFStoreFormData(null);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        array(
                            'id' => $template->getId(),
                        )
                    );
                    return;
                }
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPDFStoreFormData($formData);
                $this->_redirect(
                    '*/*/edit',
                    array(
                        'id' => $this->getRequest()->getParam('id', null)
                    )
                );
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $template = $this->_initTemplate();
        try {
            $template->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Template have been successfully deleted')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/list');
    }

    public function massDeleteAction()
    {
        $templateIds = $this->getRequest()->getParam('id', null);
        try {
            if (!is_array($templateIds)) {
                throw new Mage_Core_Exception($this->__('Invalid template id(s)'));
            }

            foreach ($templateIds as $id) {
                Mage::getSingleton('po_pdf/template')
                    ->load($id)
                    ->delete()
                ;
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('%d template(s) have been successfully deleted', count($templateIds))
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function previewAction()
    {
        if ($formData = $this->getRequest()->getPost()) {
            //prepare preview invoice
            $invoiceId = Potato_Pdf_Helper_Config::getPreviewInvoice();
            $invoice = Mage::getModel('sales/order_invoice');
            if ($invoiceId) {
                $invoice->loadByIncrementId($invoiceId);
            }
            if (!$invoice->getId()) {
                $invoice = Mage::getModel('sales/order_invoice')->getCollection()->getFirstItem();
            }
            Mage::register('current_invoice', $invoice, true);

            //prepare preview order
            $orderId = Potato_Pdf_Helper_Config::getPreviewOrder();
            $order = Mage::getModel('sales/order');
            if ($orderId) {
                $order->loadByIncrementId($orderId);
            }
            if (!$order->getId()) {
                $order = Mage::getModel('sales/order')->getCollection()
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem()
                ;
            }
            Mage::register('current_order', $order, true);

            //prepare preview shipment
            $shipmentId = Potato_Pdf_Helper_Config::getPreviewShipment();
            $shipment = Mage::getModel('sales/order_shipment');
            if ($shipmentId) {
                $shipment->loadByIncrementId($shipmentId);
            }
            if (!$shipment->getId()) {
                $shipment = Mage::getModel('sales/order_shipment')->getCollection()
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem()
                ;
            }
            Mage::register('current_shipment', $shipment, true);

            //prepare preview credit memo
            $creditMemoId = Potato_Pdf_Helper_Config::getPreviewCreditMemo();
            $creditMemo = Mage::getModel('sales/order_creditmemo');
            if ($creditMemoId) {
                $creditMemo->load($creditMemoId, 'increment_id');
            }
            if (!$creditMemo->getId()) {
                $creditMemo = Mage::getModel('sales/order_creditmemo')->getCollection()
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem()
                ;
            }
            Mage::register('current_creditmemo', $creditMemo, true);

            $template = Mage::getModel('po_pdf/template');
            $template->setContent($formData['content']);
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            //prepare html
            $html = $template->getProcessedTemplate(
                array(
                    'store_id'                  => $order->getStoreId(),
                    'invoice'                   => $invoice,
                    'invoice_formatted_date'    => Mage::helper('po_pdf/sales')->getCreatedAtFormated('long', $invoice->getCreatedAt()),
                    'shipment_formatted_date'   => Mage::helper('po_pdf/sales')->getCreatedAtFormated('long', $shipment->getCreatedAt()),
                    'creditmemo_formatted_date' => Mage::helper('po_pdf/sales')->getCreatedAtFormated('long', $creditMemo->getCreatedAt()),
                    'order'                     => $order,
                    'order_items'               => Mage::helper('po_pdf/sales')->getAllVisibleItems($order),
                    'invoice_items'             => Mage::helper('po_pdf/sales')->getAllVisibleItems($invoice),
                    'creditmemo_items'          => Mage::helper('po_pdf/sales')->getAllVisibleItems($creditMemo),
                    'shipment_items'            => Mage::helper('po_pdf/sales')->getAllVisibleItems($shipment),
                    'customer'                  => $customer,
                    'shipment'                  => $shipment,
                    'comments'                  => $order->getStatusHistoryCollection(true),
                    'creditmemo'                => $creditMemo,
                    'payment_html'              => Mage::helper('po_pdf/sales')->getPaymentInfoFromOrder($order)
                ),
                true
            );
            try {
                //convert
                $pdfContent = Potato_Pdf_Helper_Data::convertHtmlToPdf(array($html), $order->getStoreId());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('po_pdf')->__($e->getMessage())
                );
                $this->_redirectReferer();
                return;
            }

            if (!$pdfContent) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('po_pdf')->__('An error occurred while file creating')
                );
                $this->_redirectReferer();
                return;
            }

            return $this->_prepareDownloadResponse(Potato_Pdf_Helper_Data::getFileName('preview-'),
                $pdfContent, 'application/pdf'
            );
        }

    }

    public function defaultTemplateAction()
    {
        if ($formData = $this->getRequest()->getPost()) {
            $template = Mage::getModel('po_pdf/template');
            $response = array(
                'content' => $template->loadDefault($this->getRequest()->getParam('template'), $this->getRequest()->getParam('locale'))
            );
            $response = Mage::helper('core')->jsonEncode($response);
            $this->getResponse()->setBody($response);
            return $this;
        }
        $this->_redirectReferer();
    }
}