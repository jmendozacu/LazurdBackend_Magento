<?php
  //include Magento app
  require_once 'app/Mage.php';
  
  //initialize Magento
  Mage::app();
  
  echo Mage::getModel('core/date')->date('Y-m-d H:i:s')."<br>";
  
  $additionalOptions[] = array(
  		'label' => "itemopt",
  		'value' => rand(5, 10005),
  );
  echo serialize($additionalOptions);
?>