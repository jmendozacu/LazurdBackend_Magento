<?php 
   require_once 'app/Mage.php';
  	
    //initialize Magento
    Mage::app();  
	$collection = Mage::getModel('customer/customer')->getCollection();

	$i=0;
	foreach($collection as $customer)
	{
		$i++;
		// echo "<pre>"; print_r($customer->getData());exit;		
		$customer=Mage::getModel('customer/customer')->load($customer->getId());
		//$customer=Mage::getModel('customer/customer')->load('313');
		$defaultBilling  = $customer->getDefaultBillingAddress();
		if($defaultBilling)
		{
			
			if($defaultBilling->getData('region_id')==0 or $defaultBilling->getData('region_id')==''  )
			{
				
				if(($defaultBilling->getData('region')) && ($defaultBilling->getData('region') != 'Please select state or province' ))
				{
					$region=trim($defaultBilling->getData('region'));
					   
					
					$collection = Mage::getResourceModel('directory/region_collection');
					$collection->addFieldToFilter('country_id', array('eq' => 'KW'))
    						  ->addFieldToFilter('default_name', array('eq' => $region));
					
					$regionId = $collection->getFirstItem()->getRegionId();
					$regionName = $collection->getFirstItem()->getDefaultName();
					$address=array();
					$regionArray=array();
					if (is_numeric($regionId))
					{
					//	echo "<pre>--" . print_r($defaultBilling->getData())."---";		
						//print_r($collection->getFirstItem()->getData());	
						//echo "<br>" . $regionId ."-----".$customer->getId(); 
					/*	$address['customer_id'] = $customer->getId();
						$regionArray = array(
                				'region' => $regionName,
                				'region_id' => $regionId
            			);
						$address['region'] = $regionArray;
						$address['region_id'] = $regionId;
						
						//echo "<pre>"; print_r($address);
						$addressModel = Mage::getModel('customer/address');
						$addressModel->addData($address);
						
						$customer->addAddress($addressModel);
						$customer->save();
						*/
						
						$entity_id=$defaultBilling->getData('entity_id');
						$address     = Mage::getModel('customer/address')->load($entity_id);
						$address->setRegionId($regionId);
						$address->save();
						echo "<br>" . $customer->getId() ."-----". $regionId; //exit;
					//	if($customer->getId()=='210') exit;	
					}
					
				}
			}
		}
		
		
		//echo "<pre>"; print_r($defaultBilling->getData()); 	exit;
		//echo "<br>----" . $i;
	}


?>