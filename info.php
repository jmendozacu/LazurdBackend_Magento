<?php //echo date('m/d/y h:m:s');
/*echo phpinfo();*/

//Load Magento API
require_once 'app/Mage.php';
Mage::app();
$orderCollection = Mage::getResourceModel('sales/order_collection');
$orderCollection->addFieldToFilter('order_status', array(
    array('name'=>'order_status','neq'=>'canceled'),
    array('name'=>'order_status','neq'=>'delivered'), 
    array('name'=>'order_status','neq'=>'returned')            
))->addFieldToFilter('is_delay_notify', false);
$date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
$orderCollection->addFieldToFilter('shipping_arrival_date', array(
                            'lt' =>  new Zend_Db_Expr("DATE_ADD('".$date."', INTERVAL -'15:00' HOUR_MINUTE)")));
echo $date = Mage::getModel('core/date')->date('Y-m-d H:i:s').'<br />';  
echo now();
echo "<pre>";
//print_r($orderCollection->getData());
$orders ="";
foreach($orderCollection->getItems() as $order)
{
    $orderModel = Mage::getModel('sales/order');
    $orderModel->load($order['entity_id']);
    $custom_email_template = 'delay_email_template'; //Mage::getStoreConfig('customconfig_options/section_delivery/emailtemplate');
    //$email_array = explode(',',Mage::getStoreConfig('customconfig_options/section_delivery/email')); 
    $emailTemplate  = Mage::getModel('core/email_template'); //->loadDefault($custom_email_template);
    $emailTemplateVariables = $orderModel->getData();
    //$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
    $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
    $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
    //foreach($email_array as $email){
        $emailTemplate->sendTransactional($custom_email_template, 
            Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY,Mage::app()->getStore()->getId()),
            'krishnaprakash.b@ewallsolutions.com',
            'Test', 
            $emailTemplateVariables);
    //}    
}

/*require 'app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 
//replace your own orders numbers here:
$test_order_ids=array(
  '100000012',
  '100000013'
);
foreach($test_order_ids as $id){
    try{
        Mage::getModel('sales/order')->loadByIncrementId($id)->delete();
        echo "order #".$id." is removed".PHP_EOL;
    }catch(Exception $e){
        echo "order #".$id." could not be remvoved: ".$e->getMessage().PHP_EOL;
    }
}
echo "complete."; */
?>