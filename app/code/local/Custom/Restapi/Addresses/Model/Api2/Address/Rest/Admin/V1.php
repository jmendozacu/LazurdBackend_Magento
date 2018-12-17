<?php
class Custom_Restapi_Addresses_Model_Api2_Address_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {
        // $customerId = 1234; // Customer id
        // $data = array(); // Address data

        $requestData = $this->getRequest()->getBodyParams();
        $customerId = $this->getRequest()->getParam('customer_id');
        // echo json_encode($requestData['firstname']);
        $data = array(
                    "firstname"     => $requestData['firstname'],
                    "lastname"      =>  $requestData['lasttname'],
                    "street"        =>  $requestData['street'],
                    "city"          =>  $requestData['city'],
                    "country_id"    =>  $requestData['country_id'],
                    "block"         =>  $requestData['block'],
                    "building"      =>  $requestData['building'],
                    "is_gift"       =>  $requestData['is_gift'],
                    "avenue"        =>  $requestData['avenue'],
                    "flat"          =>  $requestData['flat'],
                    "receiver_name" =>  $requestData['receiver_name'],
                    "region"        =>  $requestData['region'],
                    "postcode"      =>  $requestData['postcode'],
                    "telephone"     =>  $requestData['telephone']
                );
        $customer = Mage::getModel('customer/customer');

        // // Load customer
        $customer->load($customerId);

        // // Get current address
        $address = $customer->getPrimaryBillingAddress();

        // // Do we add a new address
        $isNewAddress = false;
        if (!$address) {

            $address = Mage::getModel('customer/address');

            $address->setCustomer($customer);
            $isNewAddress = true;
        }

        // // Append data
        $address->addData($data);
        $address->save();

        if ($isNewAddress) {
            // Add address to customer and save
            $customer->addAddress($address)
                ->setDefaultBilling($address->getId())
                ->save();
        }
        echo json_encode(array('message' => 'Address added sucessfully!'));
        exit();
    }

}
?>