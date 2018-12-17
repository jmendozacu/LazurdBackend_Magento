<?php
class Custom_Restapi_Customerorders_Model_Api2_Customerorder_Rest_Admin_V1 extends Mage_Api2_Model_Resource
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

        $orderCollection = Mage::getModel('sales/order')->getCollection()
                            ->addFieldToFilter('customer_id',$this->getRequest()
                            ->getParam('customer_id'))->addFieldToSelect('*');

        foreach($orderCollection as $order){
            $orders['orders'] = $order->getData();

        }            
        echo json_encode($orders);
            exit();
    }

}
?>