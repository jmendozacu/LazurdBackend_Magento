<?php

class Potato_Pdf_Helper_Adminhtml extends Mage_Adminhtml_Helper_Data
{
    static function isAdminOrderPrintAllowed($store=null)
    {
        if (!Potato_Pdf_Helper_Config::isEnabled($store) ||
            !Potato_Pdf_Helper_Config::isOrderEnabled($store) ||
            !Potato_Pdf_Helper_Config::getOrderAdminTemplate($store)
        ) {
            return false;
        }
        return true;
    }

    static function isAdminInvoicePrintAllowed($store=null)
    {
        if (!Potato_Pdf_Helper_Config::isEnabled($store) ||
            !Potato_Pdf_Helper_Config::isInvoiceEnabled($store) ||
            !Potato_Pdf_Helper_Config::getInvoiceAdminTemplate($store)
        ) {
            return false;
        }
        return true;
    }

    static function isAdminShipmentPrintAllowed($store=null)
    {
        if (!Potato_Pdf_Helper_Config::isEnabled($store) ||
            !Potato_Pdf_Helper_Config::isShipmentEnabled($store) ||
            !Potato_Pdf_Helper_Config::getShipmentAdminTemplate($store)
        ) {
            return false;
        }
        return true;
    }

    static function isAdminCreditMemoPrintAllowed($store=null)
    {
        if (!Potato_Pdf_Helper_Config::isEnabled($store) ||
            !Potato_Pdf_Helper_Config::isCreditMemoEnabled($store) ||
            !Potato_Pdf_Helper_Config::getCreditMemoAdminTemplate($store)
        ) {
            return false;
        }
        return true;
    }
}