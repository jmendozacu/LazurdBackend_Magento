<?php

class Cac_Restapi_Customers_Model_Api2_Customer_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_CUSTOMER = "entity";
    const OPERATION_GET_CUSTOMERS_REGION = "regions";


    /**
     * Retrieve a Categoies
     * @return string
     */
    //customized _retrieve method
    public function getCustomer()
    {
        // route:
        // /cac/customer/:id
        $id = $this->getRequest()->getParam('id');

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

//        (SELECT fn.value
//            FROM customer_entity_varchar fn
//            WHERE c.entity_id = fn.entity_id AND
//    fn.attribute_id = 12) AS password_hash,

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('*')
            ->addAttributeToFilter('customer_id', $id)
            ->setOrder('created_at', 'desc');

        error_log("going to write orders");









        $customer_orders = [];


    ;

        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(array('o' => 'admin_role'), "o.user_id = main_table.user_id", array('*'));
        $collection->addFieldToFilter('parent_id', 5);
        $data_array = array();
        foreach ($collection as $key => $value) {
            $data_array[$value->getUserId()] = $value->getUsername();
        }

        $_shippingAdd=null;
        foreach ($orders as $order) {
            if (is_null($_shippingAdd))
                $_shippingAdd= $order->getShippingAddress();
            $this_order = [];
            $items = [];
            foreach ($order->getItemsCollection() as $item) {
                $simple_item["ProductName"] = $item->getProduct()->getName();
                $simple_item["Message"] = $item->getData("text_custom_options_value");
                $items[] = $simple_item;
            }
            $this_order["OrderNo"] = $order->getRealOrderId();
            $this_order["OrderDate"] = $order->getUpdatedAt();
            $this_order["OrderStatus"] = $order->getStatusLabel();
            $this_order["DriverID"] = $order->getDriverId();
            $this_order["DriverName"] = $data_array[$order->getDriverId()];
            $this_order["OrderItems"] = $items;
            $customer_orders[] = $this_order;
        }

        $query = "SELECT c.entity_id,c.email, 
            (
             
             
            SELECT fn.value
            FROM customer_entity_varchar fn
            WHERE c.entity_id = fn.entity_id AND
             fn.attribute_id = 5) AS name, 
             (
            SELECT fn.value
            FROM customer_entity_varchar fn
            WHERE c.entity_id = fn.entity_id AND 
             fn.attribute_id = 7) AS lastname,
             (
            SELECT fn.value
            FROM customer_entity_varchar fn
            WHERE c.entity_id = fn.entity_id AND 
             fn.attribute_id = 150) AS cpfcnpj, 
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 149) AS cpfcnpj2,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 145) AS rg,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 151) AS phone1,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 31) AS phone2,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 27) AS country,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 28) AS state,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 26) AS city,                 
             cat.value AS address,
             (
            SELECT fn.value
            FROM customer_address_entity_varchar fn
            WHERE ca.entity_id = fn.entity_id AND 
             fn.attribute_id = 30) AS cep
            FROM customer_entity AS c
            LEFT JOIN customer_address_entity AS ca ON c.entity_id = ca.parent_id
            LEFT JOIN customer_address_entity_text AS cat ON cat.entity_id = ca.entity_id                

            GROUP BY entity_id having entity_id=$id";

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query)[0];



        if (!is_null($_shippingAdd)) {
            $block = $_shippingAdd->getCompany();
            $street = $_shippingAdd->getStreet()[0];
            $building = $_shippingAdd->getCity();
            $country = Mage::getModel('directory/country')->loadByCode($_shippingAdd->getCountryId())->getName();
            $area = $_shippingAdd->getRegion();

            $results["FullAddress"] = "Block:$block, Street:$street, Building:$building, Area:$area, Country:$country";
        }
        else
            $results["FullAddress"]="";

        $results["orders"] = $customer_orders;

        /**
         * Print out the results
         */
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        exit();
    }
    public function getCustomersRegion(){
        // route:
        // /cac/customer/regions

        return "Regions";
    }
    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_CUSTOMER:
                $result = $this->getCustomer();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_CUSTOMERS_REGION:
                $result = $this->getCustomersRegion();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

}