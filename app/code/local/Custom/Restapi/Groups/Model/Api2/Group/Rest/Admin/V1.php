<?php
class Custom_Restapi_Groups_Model_Api2_Group_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * Create a customer
     * @return array
     */

    public function _create() {
        $requestData = $this->getRequest()->getBodyParams();
        $firstName = $requestData['firstname'];
        $lastName = $requestData['lastname'];
        $email = $requestData['email'];
        $phone = $requestData['phone'];
        $password = $requestData['password'];

        $customer = Mage::getModel("customer/customer");

        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setPhone($phone);
        $customer->setPasswordHash(md5($password));
        $customer->setWebsiteId("1");
        $customer->setStoreId("1");
        $customer->save();
        
        $customerId = $customer->load($email, 'email')->getId();
        $userData = array(
            'customerId' => $customerId,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'phone'    => $phone, 
            'email' => $email,
            'password' => $password
            );
        echo json_encode($userData);
        exit();
        // }

    }

     /**
     * Update a Customer by ID
     * @return String
     */
     public function _update(){
        $requestData = $this->getRequest()->getBodyParams();
        $firstName = $requestData['firstname'];
        $lastName = $requestData['lastname'];
        $email = $requestData['email'];
        $phone = $requestData['phone'];
        $customer = Mage::getModel("customer/customer")->load($this->getRequest()->getParam('customerId'));
        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setPhone($phone);
        $customer->setPasswordHash(md5($password));
        $customer->setWebsiteId("1");
        $customer->setStoreId("1");
        $customer->save();
        echo json_encode(array('message' => 'Customer Updated Successfully.'));
        exit();

     }

     /**
     * Retrieve a Customer by ID
     * @return array
     */
    public function _retrieve()
    {
        $customer = Mage::getModel("customer/customer")->load($this->getRequest()->getParam('customerId'));
        $userData = array(
            'customerId' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'phone'    => $customer->getPhone(), 
            'email' => $customer->getEmail(),
            );
        echo json_encode($userData);
        exit();
    }

}
?>