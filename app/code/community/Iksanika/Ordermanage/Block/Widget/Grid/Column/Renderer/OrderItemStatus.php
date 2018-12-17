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

class Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderItemStatus
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{

    public function render(Varien_Object $row)
    {
//        echo get_class($this);
//        $options = $this->getColumn()->getOptions();
//        var_dump($options);
//        die();
//        $statusList = $this->getStatusesByState($row->getData('state'));
//        var_dump($row);die();
//        return get_class($row);
//                    $value = $row->getData($this->getColumn()->getIndex());
//echo $value.'~~';
//        $allOptions = $row->getData('product_options');
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
                $orderItem = Mage::getModel('sales/order_item')->load($orderId);
                echo $orderItem->getStatus().'<br/>'.($ordersCount != ++$orderIndex ? '<hr/>' : '');
            }
        }
//        return $row->getStatus();
//        return $row->getStatus();
        /*
        $statusList = Iksanika_Ordermanage_Model_System_Config_Source_Columns_StatusesOrderItem::toOptionArray();
        if (!empty($statusList) && is_array($statusList)) 
        {
            $value = $row->getData($this->getColumn()->getIndex());
            $out = '<select name="'.$this->getColumn()->getIndex().'">';
            foreach($statusList as $item)
            {
                $out .= '<option value="'.$item['value'].'" '.($value == $item['value'] ? 'selected':'').'>'.$this->escapeHtml($item['label']).'</option>'; 
            }
            $out .= '</select>';
            return $out;
        }
         */
        
//        return '[SELECT is empty]';
    }
}
