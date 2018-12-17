<?php
class Ewall_CustomConfig_Model_Userroles
{  
  public function toOptionArray()
  {
      $rules = Mage::getModel('admin/roles')->getCollection();
      $role_array = array();
      foreach($rules as $rule){
        $getRoleName = $rule->getRoleName();
        $getRoleId = $rule->getRoleId();
        $role_array[$getRoleId] = $getRoleName;
      }
      return $role_array;
  }
}
?>