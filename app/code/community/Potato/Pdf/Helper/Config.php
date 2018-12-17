<?php

class Potato_Pdf_Helper_Config extends Mage_Core_Helper_Abstract
{
    const GENERAL_IS_ENABLED            = 'po_pdf/general/is_enabled';
    const GENERAL_LIB_PATH              = 'po_pdf/general/lib_path';
    const GENERAL_PAGE_ORIENTATION      = 'po_pdf/general/page_orientation';
    const GENERAL_PAGE_FORMAT           = 'po_pdf/general/page_format';
    const GENERAL_MARGIN_TOP            = 'po_pdf/general/margin_top';
    const GENERAL_MARGIN_LEFT           = 'po_pdf/general/margin_left';
    const GENERAL_MARGIN_RIGHT          = 'po_pdf/general/margin_right';
    const GENERAL_MARGIN_BOTTOM         = 'po_pdf/general/margin_right';
    const GENERAL_ADDITIONAL_OPTIONS    = 'po_pdf/general/additional_options';
    const GENERAL_USE_SERVICE           = 'po_pdf/general/use_service';

    const ORDER_IS_ENABLED              = 'po_pdf/order/is_enabled';
    const ORDER_ADMIN_TEMPLATE          = 'po_pdf/order/admin_template';
    const ORDER_CUSTOMER_TEMPLATE       = 'po_pdf/order/customer_template';

    const INVOICE_IS_ENABLED            = 'po_pdf/invoice/is_enabled';
    const INVOICE_ADMIN_TEMPLATE        = 'po_pdf/invoice/admin_template';
    const INVOICE_CUSTOMER_TEMPLATE     = 'po_pdf/invoice/customer_template';

    const SHIPMENT_IS_ENABLED           = 'po_pdf/shipment/is_enabled';
    const SHIPMENT_ADMIN_TEMPLATE       = 'po_pdf/shipment/admin_template';
    const SHIPMENT_CUSTOMER_TEMPLATE    = 'po_pdf/shipment/customer_template';

    const CREDIT_MEMO_IS_ENABLED        = 'po_pdf/creditmemo/is_enabled';
    const CREDIT_MEMO_ADMIN_TEMPLATE    = 'po_pdf/creditmemo/admin_template';
    const CREDIT_MEMO_CUSTOMER_TEMPLATE = 'po_pdf/creditmemo/customer_template';

    const PREVIEW_ORDER                 = 'po_pdf/preview/order_increment_id';
    const PREVIEW_INVOICE               = 'po_pdf/preview/invoice_increment_id';
    const PREVIEW_SHIPMENT              = 'po_pdf/preview/shipment_increment_id';
    const PREVIEW_CREDITMEMO            = 'po_pdf/preview/creditmemo_increment_id';

    static function useService($store = null)
    {
        return (bool)Mage::getStoreConfig(self::GENERAL_USE_SERVICE, $store);
    }

    static function getPreviewOrder($store = null)
    {
        return (string)Mage::getStoreConfig(self::PREVIEW_ORDER, $store);
    }

    static function getPreviewInvoice($store = null)
    {
        return (string)Mage::getStoreConfig(self::PREVIEW_INVOICE, $store);
    }

    static function getPreviewShipment($store = null)
    {
        return (string)Mage::getStoreConfig(self::PREVIEW_SHIPMENT, $store);
    }

    static function getPreviewCreditMemo($store = null)
    {
        return (string)Mage::getStoreConfig(self::PREVIEW_CREDITMEMO, $store);
    }

    static function getPageOrientation($store = null)
    {
        return (string)Mage::getStoreConfig(self::GENERAL_PAGE_ORIENTATION, $store);
    }

    static function getLibPath($store = null)
    {
        return (string)Mage::getStoreConfig(self::GENERAL_LIB_PATH, $store);
    }

    static function getPageFormat($store = null)
    {
        return (string)Mage::getStoreConfig(self::GENERAL_PAGE_FORMAT, $store);
    }

    static function getMarginRight($store = null)
    {
        return (int)Mage::getStoreConfig(self::GENERAL_MARGIN_RIGHT, $store);
    }

    static function getMarginLeft($store = null)
    {
        return (int)Mage::getStoreConfig(self::GENERAL_MARGIN_LEFT, $store);
    }

    static function getMarginTop($store = null)
    {
        return (int)Mage::getStoreConfig(self::GENERAL_MARGIN_TOP, $store);
    }

    static function getMarginBottom($store = null)
    {
        return (int)Mage::getStoreConfig(self::GENERAL_MARGIN_BOTTOM, $store);
    }

    static function getAdditionalOptions($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_ADDITIONAL_OPTIONS, $store);
    }

    static function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::GENERAL_IS_ENABLED, $store);
    }

    static function isOrderEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::ORDER_IS_ENABLED, $store);
    }

    static function getOrderAdminTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::ORDER_ADMIN_TEMPLATE, $store);
    }

    static function getOrderCustomerTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::ORDER_CUSTOMER_TEMPLATE, $store);
    }

    static function isInvoiceEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::INVOICE_IS_ENABLED, $store);
    }

    static function getInvoiceAdminTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::INVOICE_ADMIN_TEMPLATE, $store);
    }

    static function getInvoiceCustomerTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::INVOICE_CUSTOMER_TEMPLATE, $store);
    }

    static function isShipmentEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::SHIPMENT_IS_ENABLED, $store);
    }

    static function getShipmentAdminTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::SHIPMENT_ADMIN_TEMPLATE, $store);
    }

    static function getShipmentCustomerTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::SHIPMENT_CUSTOMER_TEMPLATE, $store);
    }

    static function isCreditMemoEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::CREDIT_MEMO_IS_ENABLED, $store);
    }

    static function getCreditMemoAdminTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::CREDIT_MEMO_ADMIN_TEMPLATE, $store);
    }

    static function getCreditMemoCustomerTemplate($store = null)
    {
        return (string)Mage::getStoreConfig(self::CREDIT_MEMO_CUSTOMER_TEMPLATE, $store);
    }
}