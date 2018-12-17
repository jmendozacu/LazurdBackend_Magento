<?php
class Custom_Restapi_Inventrystocks_Model_Api2_Inventrystock_Rest_Admin_V1 extends Mage_Api2_Model_Resource
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

      // $_item = 103; // Whatever your product ID is. You can use any method like $_product->getProductId() to get to this point.
      $item_id = $this->getRequest()->getParam('product_id');
      $products = Mage::getModel('catalog/product')->getCollection();
      foreach ($products as $product) {
        
        if ($product->getId() == $item_id) {
            
            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            $pqty = $stock->getQty();
        }
        
      }

      echo json_encode($pqty);
      exit();
    }

}
?>