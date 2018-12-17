<?php
class Custom_Restapi_Customers_Model_Api2_Customer_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {

    }

     /**
     * Retrieve a Categoies
     * @return string
     */

    public function _retrieve()
    {

    $ruleId = '2';
    $cats = Mage::getModel('catalog/category')->load($ruleId);
    $subcats  = Mage::getModel('catalog/category')->load($ruleId)->getChildren();

    $cur_category = array();
    if($subcats != '') 
    {
        foreach(explode(',',$subcats) as $subCatid)
        {
            $_category = Mage::getModel('catalog/category')->load($subCatid);
            $childcats  = Mage::getModel('catalog/category')->load($subCatid)->getChildren();

            $imageName = Mage::getModel('catalog/category')->load($subCatid)->getImage();
            $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            // $mediaUrl.'catalog/category/'.$imageName;
            $node['category_id'] = $subCatid;
            $node['name'] = $_category->getName();
            $node['parent_id'] = $_category->getParentId();
            $node['imageUrl'] = Mage::getModel('catalog/category')->load($subCatid)->getThumbnailImageUrl();
            $node['child_id'] = $childcats;
            if($_category->getIsActive()){
                $node['active'] = 1;
            }else{
                $node['active'] = 0;
            }
            $node['level'] = $_category->getLevel();
            $node['position'] = $_category->getPosition();

            $cur_category['categories'][] = $node;

        }
    }


    echo json_encode($cur_category);       
    exit();
    // echo json_encode($cur_category);       

    }

}
?>