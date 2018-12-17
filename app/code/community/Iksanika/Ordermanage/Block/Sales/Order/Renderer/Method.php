<?php
class Iksanika_OrderManage_Block_Sales_Order_Renderer_Method extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$order_id = (int)$row->getData($this->getColumn()->getIndex());
		$collection = Mage::getModel('sales/order')->load($order_id);
		if($collection->getShippingMethod() == 'flatrate_flatrate' ||$collection->getShippingMethod() == 'flatrate2_flatrate2')
		{
			$order_data = Mage::getModel('sales/order')->load($order_id);
			if($order_data->getShippingArrivalComments() > 12){
				$time = ($order_data->getShippingArrivalComments()-12).'PM';
			}
			elseif($order_data->getShippingArrivalComments() == 12){
                $time = ($order_data->getShippingArrivalComments()).'PM' ;
            }
            else{
                $time = $order_data->getShippingArrivalComments().'AM';
            }
            $date_format = Mage::getStoreConfig('deliverydate/deliverydate_general/deliverydate_format');
            $time_format = Mage::getStoreConfig('deliverydate/deliverydate_general/deliverytime_format');
            if ($date_format=='')
                $date_format='d/M/Y';
            if($time_format=='')
                $date_format.=" ,g:i a";
            else
                $date_format.=" ".$time_format;

            if ($order_data->getShippingArrivalDate()!='')
                return $collection->getShippingDescription().'<br>'.date($date_format,strtotime($order_data->getShippingArrivalDate()));
            else           
				return $collection->getShippingDescription().'<br>'.'N/A';
                //return $collection->getShippingDescription().'<br>'.date($date_format,strtotime($order_data->getShippingArrivalDate()));
		}
		else
		{
			return $collection->getShippingDescription();
		}
	}

}