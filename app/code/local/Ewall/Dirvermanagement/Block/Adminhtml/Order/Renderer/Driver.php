<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_Renderer_Driver extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $dispatch_user = Mage::getStoreConfig('customconfig_options/section_two/dispatch');
        $drvrId = $row->getDriverId();
        if($role_data->getRoleId() != $dispatch_user && $role_data->getRoleId() != 1){
			$username = $role_data = Mage::getModel('admin/user')->load($drvrId)->getUsername();
			return $username;
		}

        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(array('o'=> 'admin_role'), "o.user_id = main_table.user_id" ,array('*'));
        $collection->addFieldToFilter('parent_id' , 5);
        $data_array=array();
        foreach ($collection as $key => $value) {
          $data_array[$value->getUserId()] = $value->getUsername();
        }

        $html = '<select name="assign_driver" class="select" id = "assign_driver_'.$row->getId().'" style="width: 75px;" onchange="updateField(this, '. $row->getId() .');return false;">';
        $html .= '<option value="">Choose Driver</option>';
		foreach ($data_array as $key => $driver) {
	        if($drvrId == $key){
	        	$class ="selected";
	        }
	        else{
	        	$class ="";
	        }
			$html .= '<option value="'.$key.'"'.$class.'> '.$driver.' </option>';
		}
		$html .='</select>';
       // $html .= ' <button style="margin : 5px" onchange="updateField(this, '. $row->getId() .');return false;">' . Mage::helper('dirvermanagement')->__('Update') . '</button>';

        return $html;
    }
}
?>
