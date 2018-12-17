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

class Iksanika_Ordermanage_Model_System_Config_Source_Columns_StatusesOrderItem
{

    public static function toOptionArray()
    {
        $statusList = Mage_Sales_Model_Order_Item::getStatuses();
        
        $columns    = array(array('value' => '', 'label' => ''));
        //$columns    = array();
        foreach($statusList as $statusItemValue => $statusItemLabel)
        {
            $columns[] = array('value' => $statusItemValue, 'label' => $statusItemLabel);
        }
        return $columns;
    }
}