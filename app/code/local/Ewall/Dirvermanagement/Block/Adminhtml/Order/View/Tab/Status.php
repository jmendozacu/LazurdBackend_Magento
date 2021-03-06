<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_View_Tab_Status
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('ewall/dirvermanagement/order/view/tab/status.phtml');
    }

    public function getTabLabel() {
        return $this->__('Assign Order Status');
    }

    public function getTabTitle() {
        return $this->__('Assign Order Status');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getOrder(){
        return Mage::registry('current_order');
    }

    public function getOptionArray()
    {
       //  $collection = Mage::getModel('sales/order_status')->getResourceCollection();
        $data_array=array();

        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $role_id = $role_data->getRoleId();
        $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');

        if($userrole_status->getAllowedStatus()){
            $role_status = explode(',', $userrole_status->getAllowedStatus());
        }
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];
        }
        foreach ($role_status as $status) {
            $data_array[$status] = $customstatus[$status];
        }

      //  echo "<pre>";print_r($data_array);
        return($data_array);
    }
    public function getFormAction(){
        return Mage::helper('adminhtml')->getUrl('dirvermanagement/adminhtml_driver/assignstatus',array('store'=>Mage::app()->getRequest()->getParam('store')));
    }
}


