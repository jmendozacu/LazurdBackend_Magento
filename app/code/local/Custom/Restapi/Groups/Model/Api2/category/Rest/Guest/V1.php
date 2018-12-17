<?php
class Custom_Restapi_Groups_Model_Api2_Category_Rest_Guest_V1 extends Mage_Api2_Model_Resource
{

    /**
     * Create a customer group
     * @return array
     */

    public function _create() {
        $str = "this is random string";
        exit($str);
        //Create Customer Group
        // $requestData = $this->getRequest()->getBodyParams();
        // $groupName = $requestData['name'];
        // Mage::getSingleton('customer/group')->setData(
        //     array('customer_group_code' => $groupName,'tax_class_id' => 3))
        //     ->save();

        // $targetGroup = Mage::getSingleton('customer/group');
        // $groupId = $targetGroup->load($groupName, 'customer_group_code')->getId();

        // if($groupId) {
        //     $json = array('id' => $groupId);
        // $firstName = 'firstname';
        // $lastName = 'lastname';
        // $email = 'firstlast44@email';
        // $password = 'password';

        // $customer = Mage::getModel("customer/customer");

        // $customer->setFirstname($firstName);
        // $customer->setLastname($lastName);
        // $customer->setEmail($email);
        // $customer->setPasswordHash(md5($password));
        // $customer->save();
        // $targetCustomer = Mage::getSingleton('customer/customer');
        // $customerId = $customer->load($email, 'email')->getId();
       // return  json_encode($customer);
            // echo json_encode($customerId);
            // exit();
        // }

    }

     /**
     * Retrieve a group name by ID
     * @return string
     */

    public function _retrieve()
    {
        // $requestData = $this->getRequest()->getBodyParams();
        $firstName = 'firstname';
        $lastName = 'lastname';
        $email = 'firstlast@email';
        $password = 'password';

        $customer = Mage::getModel("customer/customer");

        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setEmail($email);
        $customer->setPasswordHash(md5($password));
        $customer->save();

       return  json_encode($customer);
        //retrieve a group name by ID
        // exit('lkjkjlkjkjlkjjjjlk');
        // $customerGroupId = $this->getRequest()->getParam('id');
        // $groupname = Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();

        // return $groupname;

    }

}
?>