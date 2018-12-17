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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Helper_Shift extends Mage_Core_Helper_Abstract
{
    /**
     * @return int
     */
    public function getCurrentShiftId()
    {
        $staffId = Mage::helper('webpos/permission')->getCurrentUser();
        $staffModel = Mage::getModel('webpos/user')->load($staffId);
        $locationId = $staffModel->getLocationId();
        //@@TODO model shift
//        $shiftModel = Mage::getModel('webpos/shift_shift');
//        $shiftId = $shiftModel->getCurrentShiftId($staffId);
        $shiftId = 9999;
        return $shiftId;
    }

    /**
     * @param $shiftId
     * @return array
     */
    public function prepareOfflineShiftData($shiftId){
        echo 'prepareOfflineShiftData'; die;
//        $shiftModel = $this->_shiftFactory->create();
//        $shiftModel->load($shiftId, "shift_id");
//        $shiftData = $shiftModel->getData();
//        $shiftData = $shiftModel->updateShiftDataCurrency($shiftData);
//
//        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
//        $saleSummaryModel = $this->_saleSummaryFactory->create();
//        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
//        $cashTransactionModel = $this->_cashTransactionFactory->create();
//        //get all sale summary data of the shift with id=$itemData['shift_id']
//        $saleSummaryData = $saleSummaryModel->getSaleSummary($shiftId);
//        //get all cash transaction data of the shift with id=$itemData['shift_id']
//        $transactionData = $cashTransactionModel->getByShiftId($shiftId);
//        //get data for zreport
//        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($shiftId);
//
//        $shiftData["sale_summary"] = $saleSummaryData;
//        $shiftData["cash_transaction"] = $transactionData;
//        $shiftData["zreport_sales_summary"] = $zReportSalesSummary;
//
//        $shiftModel->updateTotalSales($zReportSalesSummary['grand_total']);
//
//        return $shiftData;
    }


}
