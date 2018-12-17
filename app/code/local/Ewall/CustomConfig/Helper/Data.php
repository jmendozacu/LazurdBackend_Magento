<?php
/**
 * Sample Widget Helper
 */
class Ewall_CustomConfig_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomStatus(){
		return array(array('value' => 'pending', 'label' => 'Need Approval'),
			array('value' => 'approved', 'label' => 'Approved'),
			array('value' => 'canceled', 'label' => 'Canceled'),
			array('value' => 'ready', 'label' => 'Ready'),
            array('value' => 'correction', 'label' => 'Correction'),
			array('value' => 'dispatched', 'label' => 'Dispatched'),
            array('value' => 'waitting', 'label' => 'Waitting'),
			array('value' => 'delivered', 'label' => 'Delivered'),
            array('value' => 'returned', 'label' => 'Returned'),
        	array('value' => 'closed', 'label' => 'Closed'));
	}

    public function getOrderStatus(){
        return array(
            'pending' => 'Need Approval',
            'approved' => 'Approved',
            'ready' => 'Ready',
            'delivered' => 'Delivered'
        );
    }

	public function getCustomCategory(){
		$categories = array();
        $allCategoriesCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('level', array('gt'=>'0'));
        $allCategoriesArray = $allCategoriesCollection->load()->toArray();
        $categoriesArray = $allCategoriesCollection
            ->addAttributeToSelect('level')
            ->addAttributeToSort('path', 'asc')
            ->addFieldToFilter('is_active', array('eq'=>'1'))
            ->addFieldToFilter('level', array('gt'=>'1'))
            ->load()
            ->toArray();
        foreach ($categoriesArray as $categoryId => $category)
        {
            if (!isset($category['name'])) {
                continue;
            }
            $categoryIds = explode('/', $category['path']);
            $nameParts = array();
            foreach($categoryIds as $catId) {
                if($catId == 1) {
                    continue;
                }
                $nameParts[] = $allCategoriesArray[$catId]['name'];
            }

            $NewNameParts = array();
            foreach($nameParts as $val){
                if(end($nameParts) == $val){
                    $NewNameParts[] = $val;
                }
                else{
                    $NewNameParts[] = '..';
                }
            }
            $categories[$categoryId] = array(
                'value' => $categoryId,
                'label' => implode(' / ', $NewNameParts)
            );
        }

        return $categories;
	}

    public function getGuestAddress($id){
        return Mage::getModel('sales/order_address')->load($id);
    }

    public function getSelectedCategory($user_id){
        $category = Mage::getModel('customconfig/usercategory')->load($user_id, 'user_id');
        return $category['cat_ids'];
    }

    public function getSelectViewedStatus($rold_id){
        //echo $role_id;
        $rold_ids = Mage::getModel('customconfig/customconfig')->load($rold_id, 'role_id');
        return $rold_ids['viewd_status'];
    }

    public function getSelectAllowedStatus($rold_id){
        $rold_ids = Mage::getModel('customconfig/customconfig')->load($rold_id, 'role_id');
        return $rold_ids['allowed_status'];
    }

    public function getWebposStaffIds(){
        $webshopusers = Mage::getModel('webpos/user')->getCollection()->getData();
        //$options = array();
        $options = array(array('value'=>'', 'label'=>'Please Select'));
        foreach ($webshopusers as $webshopusers_data) {
            $options[] = array('value'=>$webshopusers_data['user_id'],'label'=>$webshopusers_data['username']);
        }
        return $options;
    }
    public function getSelectedWebposStaffId($user_id){
        $category = Mage::getModel('customconfig/usercategory')->load($user_id, 'user_id');
        return $category['pos_user'];
    }
    public function getKitchenuserReadystatus($role_id,$order_id){
        $kitchenuserRoleId = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        if($kitchenuserRoleId == $role_id){
            $order = Mage::getModel('sales/order')->load($order_id)->getItemsCollection();
            $disabled = '';
            foreach ($order as $key => $value) {
                if($value->getItemStatus() == ''){
                    $disabled = 'disabled';
                }
            }
            return $disabled;
        }else{
            return '';
        }
    }
}
?>