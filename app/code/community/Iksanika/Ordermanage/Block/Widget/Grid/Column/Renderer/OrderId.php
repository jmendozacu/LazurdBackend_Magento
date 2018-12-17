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

class Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderId
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $allOptions = $row->getData('ordered_item_ids');
        $allOrdersList = explode(',', $allOptions);
        $ordersCount = count($allOrdersList);
        $orderIndex = 0;
        foreach($allOrdersList as $orderId)
        {
            $enabled = false;
            if(!Mage::getStoreConfig('ordermanage/orderedItemsMode/group'))
            {
                $enabled = true;
            }elseif(Mage::getStoreConfig('ordermanage/orderedItemsMode/group') && (Mage::getStoreConfig('ordermanage/orderedItemsMode/orderLimit') != $orderIndex))
            {
                $enabled = true;
            }
            
            if($enabled)
            {
                echo $orderId.'<br/>'.($ordersCount != ++$orderIndex ? '<hr/>' : '');
            }
        }
        //return $this->_getValue($row);
    }
}
