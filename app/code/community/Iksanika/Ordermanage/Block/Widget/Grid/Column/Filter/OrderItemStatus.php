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

class Iksanika_Ordermanage_Block_Widget_Grid_Column_Filter_OrderItemStatus
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    function getCondition()
    {
        if(trim($this->getValue())=='')
            return null;
        
/*        $skuIds = explode(',', $this->getValue());
        $skuIdsArray = array();
        foreach($skuIds as $skuId)
            $skuIdsArray[] = trim($skuId);
        if(count($skuIdsArray) == 1)
        {
            $helper = Mage::getResourceHelper('core');
            $likeExpression = $helper->addLikeEscape($this->getValue(), array('position' => 'any'));
            return array('like' => $likeExpression);
        }
        else
            return array('inset' => $skuIdsArray);
 */
/*        $statusList = Iksanika_Ordermanage_Model_System_Config_Source_Columns_StatusesOrderItem::toOptionArray();
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
    }
}
