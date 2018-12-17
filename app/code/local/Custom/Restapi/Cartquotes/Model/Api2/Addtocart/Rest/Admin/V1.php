<?php
class Custom_Restapi_Cartquotes_Model_Api2_Addtocart_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {
    }

    public function _retrieve() {
    }

    public function _update() {
                
        echo json_encode('Update Called!');
        exit();
    }
    public function _delete() {
        
        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($this->getRequest()->getParam('cart_id'));
         $quoteItems=$quote->getAllItems();
         if(count($quoteItems)>0){
             foreach($quoteItems as $oneItem){
                 if($oneItem->getProductId()==$this->getRequest()->getParam('product_id')){
                   $quote->removeItem($oneItem->getId());
                   $quote->collectTotals()->save();
                 }
             }
         }
        echo json_encode("Cart Item removed!");
        exit();
    }

}
?>