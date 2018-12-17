<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Ordermanage_Block_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function getJsObjectName()
    {
        return $this->getId().'JsObjectIKSOrdermanage';
    }

    public function _exportIterateCollection($callback, array $args)
    {
        $paymentMethod = false;
        $paymentCode = false;
        $paymentCCType = false;
        foreach ($this->getColumns() as $_column)
        {
            if($_column->getData('index') == 'payment_method')
                $paymentMethod = true;
            if($_column->getData('index') == 'payment_code')
                $paymentCode = true;
            if($_column->getData('index') == 'payment_cc_type')
                $paymentCCType = true;
                
        }
        
        $originalCollection = $this->getCollection();
        $count = null;
        $page  = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize($this->_exportPageSize);
            $collection->setCurPage($page);
            $collection->load();
            if (is_null($count)) {
                $count = $collection->getSize();
                $lPage = $collection->getLastPageNumber();
            }
            if ($lPage == $page) {
                $break = true;
            }
            $page ++;

            foreach ($collection as $item) {
                if($paymentMethod)
                    $item->setData('payment_method', $item->getPayment()->getMethodInstance()->getTitle());
                if($paymentCode)
                    $item->setData('payment_code', $item->getPayment()->getMethodInstance()->getCode());
                if($paymentCCType)
                    $item->setData('payment_cc_type', $item->getPayment()->getData('cc_type') == null ? '[NO CC TYPE]' : $item->getPayment()->getData('cc_type'));
                call_user_func_array(array($this, $callback), array_merge(array($item), $args));
            }
        }
    }
    

}
