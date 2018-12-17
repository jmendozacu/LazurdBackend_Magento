<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Order_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $role_id = $role_data->getRoleId();
        $driver_userId =  Mage::getStoreConfig('customconfig_options/section_two/driver');
        $dispatch_userId =  Mage::getStoreConfig('customconfig_options/section_two/dispatch');
        $operation_userId =  Mage::getStoreConfig('customconfig_options/section_two/operation');       
        $order_status = $row->getOrderStatus();

        $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');
        if($userrole_status->getAllowedStatus()){
            $role_status = explode(',', $userrole_status->getAllowedStatus());
        }
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];

        }
        if($role_id == $operation_userId){
            return $customstatus[$order_status];
        }
        foreach ($role_status as $status) {
            if($role_id != 1){
                $data_array[$status] = $customstatus[$status];
            }
        }
        if($role_id == 1){
            $data_array = array();
            $data_array = $customstatus;
        }
        if($role_id != $driver_userId || $role_id != $dispatch_userId){
            $html = '<select name="assign_status" class="select" id = "assign_status_'.$row->getId().'" style="width: 115px;" 
            onFocus="setAssignstatusValues(this, '. $row->getId() .');return false;" onChange="updateStatus(this, '. $row->getId() .');return false;" '.Mage::helper('customconfig')->getKitchenuserReadystatus($role_data->getRoleId(),$row->getId()).'>';
            $html .= '<option value="">Please Select Status...</option>';
        }

        if($role_id == $dispatch_userId){
            $html = '<select name="assign_status" class="select" id = "assign_status_'.$row->getId().'" style="width: 115px;" 
            onFocus="setAssignstatusValues(this, '. $row->getId() .');return false;" onChange="updateDispatchStatus(this, '. $row->getId() .');return false;">';
            $html .= '<option value="">Please Select Status...</option>';
        }

		foreach ($data_array as $key => $driver) {
	        if($order_status == $key){
	        	$class ="selected";
	        }
	        else{
	        	$class ="";
	        }
            $html .= '<option value="'.$key.'"'.$class.'> '.$driver.' </option>' . $key .', '. $row->getId() .'</option>';
		}
        if($role_id != $driver_userId){
		  $html .='</select>';
        }
        return $html;
    }
}
?>
