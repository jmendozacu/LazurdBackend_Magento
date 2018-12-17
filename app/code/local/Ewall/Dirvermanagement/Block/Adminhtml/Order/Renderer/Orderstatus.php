<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_Renderer_Orderstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];
        }
        return $customstatus[trim($row->getOrderStatus())];
    }
}
?>
