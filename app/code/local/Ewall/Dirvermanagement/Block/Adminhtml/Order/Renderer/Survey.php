<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_Renderer_Survey extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {           
        $orderModel = Mage::getModel('sales/order')->load($row->getEntityId());        
        if($html == '2'){
            //$html = '';            
            $html = Mage::helper('sales')->__('No');
        }
        else{
            $html = Mage::helper('sales')->__('Yes');
            /*$html = $orderModel->getIsSurvey();
            if($html == '1'){
                $html = Mage::helper('sales')->__('Yes');
            }else{
                $html = Mage::helper('sales')->__('No');
            }*/
        }
        return $html;
    }
}
?>
