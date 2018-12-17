<?php
class Custom_Restapi_Categoryproducts_Model_Api2_Categoryproduct_Rest_Admin_V1 extends Mage_Api2_Model_Resource
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
        $limit = $this->getRequest()->getParam('limit');
        $page = $this->getRequest()->getParam('page');
        $priceLessThan = $this->getRequest()->getParam('price_less_than');
        $priceGreaterThan = $this->getRequest()->getParam('price_greater_than');
        $name = $this->getRequest()->getParam('name');
        $description = $this->getRequest()->getParam('description');
        $category_id = $this->getRequest()->getParam('cat_id');
        $sort = $this->getRequest()->getParam('sort');
        if(NULL == $limit){
            $limit = '10';
        }
        if(NULL == $page){
            $page = '1';
        }
        if (NULL == $sort) {
            $sort = 'asc';
        }

        $collection = Mage::getModel('catalog/product')->getCollection()->setPageSize($limit)->setCurPage($page)
        ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');
        if($name){
            $collection->addAttributeToFilter('name', array('like' => $name.'%'));            
        }
        if($description){
            $collection->addAttributeToFilter('description', array('like' => $description.'%'));            
        }
        if ($priceLessThan) {
            $collection->addAttributeToFilter('price', array('lt' => $priceLessThan));            
        }
        if ($priceGreaterThan) {
            $collection->addAttributeToFilter('price', array('gt' => $priceGreaterThan));
        }


        $collection->addAttributeToFilter('category_id', array('in' => array($category_id)))->setOrder('name', $sort);
        foreach ($collection as $coll) {
            $_product = Mage::getModel('catalog/product')->load($coll->getId());
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($coll);
            $products['products'][] = array(
                'productId' => $_product->getId(),
                'name' => $_product->getName(),
                'price' => $_product->getPrice(),
                'description' => $_product->getDescription(),
                'quantity' => $stock->getQty(),
                'product_url' => $_product->getProductUrl(),
                'image_url' => $_product->getImageUrl()
            );
        }
        echo json_encode($products);
        exit();
        //Old code
    //     $limit = $this->getRequest()->getParam('limit');
    //     $page = $this->getRequest()->getParam('page');

    //     // echo json_encode($page);
    //     // exit();
    //     if(NULL == $limit){
    //         $limit = '3';
    //     }
    //     if(NULL == $page){
    //         $page = '1';
    //     }
    // $category = new Mage_Catalog_Model_Category();
    //     $category->load($this->getRequest()->getParam('cat_id'));
    //     $prodCollection = $category->getProductCollection()->setPageSize($limit)->setCurPage($page);
    //     foreach ($prodCollection as $product) {
    //         $prdIds[] = $product->getId(); ///Store all th eproduct id in $prdIds array
    //     }

    //     foreach($prdIds as $_prdIds){
    //         $product_id = $_prdIds;
    //         $obj = Mage::getModel('catalog/product');
    //         $_product = $obj->load($product_id);
    //         $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
    //         $products['products'][] = array(
    //             'productId' => $_product->getId(),
    //             'name' => $_product->getName(),
    //             'price' => $_product->getPrice(),
    //             'description' => $_product->getDescription(),
    //             'quantity' => $stock->getQty(),
    //             'product_url' => $_product->getProductUrl(),
    //             'image_url' => $_product->getImageUrl()
    //             );

    //     }

    //     $names = array();
    //     foreach ($products['products'] as $key => $row)
    //     {
    //         $names[$key] = $row['name'];
    //     }
    //     array_multisort($names, SORT_ASC, $products['products']);
    //     echo json_encode($products);       
    //     exit();
    }

}
?>