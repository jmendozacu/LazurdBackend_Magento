<?php
class Custom_Restapi_Customerlogin_Model_Api2_Customerlogin_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {
        
        $requestData = $this->getRequest()->getBodyParams();
        $email = $requestData['email'];
        $password = $requestData['password'];

        $customer = Mage::getModel('customer/customer')->setWebsiteId('1');

        if( $customer->authenticate($email,$password) ){
            
            $customer->loadByEmail($email);
            $customerData = array(
            'customerId' => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'phone'    => $customer->getPhone(), 
            'email' => $email
            );

            echo json_encode($customerData);
        }else{
            echo json_encode(array('message', 'Incorrect email OR password'));
        }
        
        exit();
    }

    public function _update(){
        
        $requestData = $this->getRequest()->getBodyParams();
        $email = $requestData['email'];
        $password = $requestData['password'];
        $new_password = $requestData['new_password'];

        try {
             $login_customer_result = Mage::getModel('customer/customer')->setWebsiteId(1)->authenticate($email, $password);
             $validate = 1;
        }
        catch(Exception $ex) {
             $validate = 0;
        }
        if($validate == 1) {
             try {
                  $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('customerId'));
                  $customer->setPassword($new_password);
                  $customer->save();
                  $result = 'Your Password has been Changed Successfully';
             }
             catch(Exception $ex) {
                  $result = 'Error : '.$ex->getMessage();
             }
        }
        else {
             $result = 'Incorrect Old Password.';
        }
        echo json_encode(array('message' => $result));
    }

}
?>