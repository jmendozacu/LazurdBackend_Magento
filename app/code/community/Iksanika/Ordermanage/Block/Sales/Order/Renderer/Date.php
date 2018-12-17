<?php
class Iksanika_OrderManage_Block_Sales_Order_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return $row->getData('shipping_delivery_date');
	}
}