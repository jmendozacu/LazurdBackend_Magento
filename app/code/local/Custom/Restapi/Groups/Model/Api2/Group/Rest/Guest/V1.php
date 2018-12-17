<?php
class Custom_Restapi_Groups_Model_Api2_Group_Rest_Guest_V1 extends Mage_Api2_Model_Resource
{

    /**
     * Create a customer group
     * @return array
     */

    public function _create() {
        exit('lkjkljkjlkjlkjlkj');
        $requestData = $this->getRequest()->getBodyParams();
        $firstName = $requestData['firstname'];
        $lastName = $requestData['lastname'];
        $email = $requestData['email'];
        $password = $requestData['password'];

        $customer = Mage::getModel("customer/customer");

        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setPasswordHash(md5($password));
        $customer->save();
        
        $customerId = $customer->load($email, 'email')->getId();
        $userData = array(
            'customerId' => $customerId,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $password
            );
        echo json_encode($userData);
        exit();
        // }

    }

     /**
     * Retrieve a group name by ID
     * @return string
     */

    public function _retrieve()
    {
    }

}
?>