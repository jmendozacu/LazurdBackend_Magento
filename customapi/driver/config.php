<?php
    $responsecodes = array(200=>"Success",
                           400=>'Bad Request',
                           401=>'Unauthorized',
                           403=>'Forbidden',
                           404=>'Not Found',
                           500=>'Internal Server Error',
                           601=>'Data Dupliacation',
                           602=>'Could Not Save',
                           603=>'No data found');
    
    $protocol = array('soap' => 'api/soap/?wsdl',
                      'v2_soap' => 'api/v2_soap/?wsdl',
                      'xmlrpc' => 'api/xmlrpc/'
                    );
    
    //$baseurl = 'http://wasally.com/demo';
    $baseurl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'];
    
    $protocolKey = 'v2_soap';
    $api_username = 'productlist';
    $api_key = 'reset!23';
    
    ini_set("soap.wsdl_cache_enabled", "0");
    ini_set('soap.wsdl_cache_ttl', '0');
    
    
    $mageFilename = '../../app/Mage.php';
    require_once $mageFilename;
    
    require_once 'config.php';
    umask(0);
    Mage::app();
    
    function setHeader($status)
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $responsecodes[$status];
        $content_type="application/json; charset=utf-8";
        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "lazurd");
		
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
    }
    
?>