<?php
class Custom_Restapi_Cartquotes_Model_Api2_Cartquote_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {
        
        $requestData = $this->getRequest()->getBodyParams();
        $customerID = $requestData['customerId'];
        $product_id = $requestData['productId'];
        $quote_id = $requestData['quote_id'];
        $product_qty = $requestData['qty'];

        try{
            $product = Mage::getModel('catalog/product')->load($product_id);
            $existingQuote = Mage::getModel('sales/quote')->setStoreId(1)->load($quote_id);

            if($existingQuote->getId()){
                if($existingQuote->hasProductId($product_id)){
                    $cartItems = $existingQuote->getAllVisibleItems();
                    if($cartItems){
                        foreach ($cartItems as $item) {
                            if($item->getProductId() == $product_id){
                                $existingQuote->updateItem($item->getId(),array('qty' => $product_qty));
                                $existingQuote->collectTotals()->save();
                            }
                        }    
                    }
                }else{
                    $existingQuote->addProduct($product, $product_qty);
                    $existingQuote->collectTotals()->save();  
                }
                
                echo json_encode(array("quote_id" => $existingQuote->getId() , "itemCount" => count($existingQuote->getAllVisibleItems()) ));
            }else{
                $customer = Mage::getModel('customer/customer')->load($customerID);
                $newQuote = Mage::getModel('sales/quote')->setStoreId(1)->loadByCustomer($customer);
                $newQuote->addProduct($product, $product_qty);   
                $newQuote->collectTotals()->save();
                echo json_encode(array("quote_id" => $newQuote->getId(), "itemCount" => count($newQuote->getAllVisibleItems()) ));
            }
                
            }
        catch(Exception $e)
        {
            echo $e->getMessage();;
        }
        exit();
    }

    public function _retrieve() {
        //1547
        $quote = Mage::getModel('sales/quote')->setStoreId(1)->load($this->getRequest()->getParam('quote_id'));
        $cartItems = $quote->getAllVisibleItems();
        foreach ($cartItems as $item) {
                $product2 = Mage::getModel('catalog/product')->load($item->product_id);
                $productMediaConfig = Mage::getModel('catalog/product_media_config');
                $baseImageUrl = $productMediaConfig->getMediaUrl($product2->getImage());
                $price2=0;
                $price2=$product2->getFinalPrice();
                $itemDetails['cartItems'][]=array(
                  "productId"=>$item->product_id,
                  "name"=>$item->name,
                  "price"=>$product2->getPrice(),
                  "spprice"=>$price2,
                  "imageurl"=>$baseImageUrl,
                  "qty"=>$item->qty
                  );
            // $product[] = Mage::getModel('catalog/product')->load($productId);
            // Do something more
        }
        echo json_encode($itemDetails);
        exit();
    }

    public function _update() {
                
        echo json_encode('Update Called!');
        exit();
    }
    public function _delete() {
                
        echo json_encode('Delete Called!');
        exit();
    }

}
?>