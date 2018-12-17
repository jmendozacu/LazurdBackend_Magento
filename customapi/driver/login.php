<?php
ini_set('max_execution_time', 120);
ini_set('default_socket_timeout', 120);

include('config.php');

if(isset($_REQUEST['username']) && $_REQUEST['username'] != null &&
   isset($_REQUEST['password']) && $_REQUEST['password'] != null)
{
    try
    {
        
        $user = Mage::getModel('admin/user'); // user your admin username
        $result = $user->login($_REQUEST['username'], $_REQUEST['password']);
        
        //echo "<pre>";print_r($result->getData());die;
        
        if($result->getData() != array())
        {
            setHeader(200);
            $response['code'] = 200;
            $response['status'] = $responsecodes[$response['code']];
            $response['driver_id'] = $result->getUserId();
            $response['firstname'] = $result->getFirstname();
            $response['lastname'] = $result->getLastname();
            $response['email'] = $result->getEmail();
            $response['username'] = $result->getUsername();
         
            echo json_encode($response);
            die;
        }
        else
        {
            setHeader(200);
            echo json_encode(array('code'=>200,'status'=>'error','message'=>utf8_encode('Invalid login or password.')));
            die;
        }
    }
    catch(Exception $e)
    {
        $e = json_decode(json_encode($e), true);
        include('exception_handler.php');
    }
}
else
{
    setHeader(400);
    echo json_encode(array('code'=>400,'status'=>'error','message'=>utf8_encode($responsecodes['400'])));
    die;
}
?>