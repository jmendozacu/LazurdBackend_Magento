<?php

class Cac_Restapi_Users_Model_Api2_User_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_USER = "entity";


    /**
     * Retrieve a Categoies
     * @return string
     */
    //customized _retrieve method
    public function getUserProfile()
    {
        $apiUser=$this->getApiUser();
        $user_object=Mage::getModel('admin/user')->load($apiUser->getUserId());
        $role_data = $user_object->getRole()->getData();

        $user['user_id']=$apiUser->getUserId();
        $user['role_id']=$role_data['role_id'];
        $user['role_name']=$role_data['role_name'];
        $profile=$user_object->getData();
        $user['firstname']=$profile['firstname'];
        $user['lastname']=$profile['lastname'];
        return $user;
    }

    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_USER:
                $result = $this->getUserProfile();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

}