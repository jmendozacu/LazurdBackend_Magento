<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_View_Tab_Driver
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('ewall/dirvermanagement/order/view/tab/driver.phtml');
    }

    public function getTabLabel() {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $dispatch_user = Mage::getStoreConfig('customconfig_options/section_two/dispatch');
        if($role_data->getRoleId() == $dispatch_user || $role_data->getRoleId() == 1){
            return $this->__('Assign to Driver');
        }
    }

    public function getTabTitle() {
        return $this->__('Assign to Driver');
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
        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(array('o'=> 'admin_role'), "o.user_id = main_table.user_id" ,array('*'));
        $collection->addFieldToFilter('parent_id' , 5);
        $data_array=array();
        foreach ($collection as $key => $value) {
          $data_array[$value->getUserId()] = $value->getUsername();
        }
        return($data_array);
    }
    public function getFormAction(){
        return Mage::helper('adminhtml')->getUrl('dirvermanagement/adminhtml_driver/assigndriver',array('store'=>Mage::app()->getRequest()->getParam('store')));
    }
}


