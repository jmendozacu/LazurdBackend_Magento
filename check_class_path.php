<?php 
require_once ('app/Mage.php'); 
Mage::app("default"); 
$reflector = new ReflectionClass('Mage_Reports_Model_Resource_Order_Collection'); 
$t = $reflector->getFileName(); 
echo $t;