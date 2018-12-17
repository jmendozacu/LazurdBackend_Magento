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
 * Inventorysuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Helper_Data extends
    Mage_Core_Helper_Abstract
{

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig( $path )
    {
        return Mage::getStoreConfig($path, Mage::app()->getStore()->getId());
    }

    /**
     * Parses the string into variables
     *
     * @param string $str
     * @param array $arr
     */
    public function parseStr(
        $str,
        array &$arr = null
    ) {
        return parse_str($str, $arr);
    }

    /**
     * get adjust stock change
     *
     * @param
     * @return boolean
     */
    public function getAdjustStockChange()
    {
        return $this->getStoreConfig('inventorysuccess/stock_control/adjust_stock_change');
    }

    /**
     * check if barcode extension is installed.
     * @return bool
     */
    public static function isBarcodeInstalled()
    {
        /** @var Mage_Core_Helper_Abstract $coreHelper */
        $coreHelper = Mage::helper('core');
        if ( $coreHelper->isModuleEnabled('Magestore_Barcodesuccess')
             && $coreHelper->isModuleOutputEnabled('Magestore_Barcodesuccess')
        ) {
            return true;
        }
        return false;
    }

}