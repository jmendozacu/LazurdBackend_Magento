<?php 
    class Ewall_CustomConfig_Model_Observer{
        
    public function appendMyNewCustomFiled(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!isset($block)) {
            return $this;
        }
        
        $user_id = Mage::app()->getRequest()->getParam('user_id');
        $role_data = Mage::getModel('admin/user')->load($user_id)->getRole()->getData();    
        
        if ($block->getType() == 'adminhtml/permissions_user_edit_form' && $role_data['role_id'] == Mage::getStoreConfig('customconfig_options/section_two/kitchen')) {
            $form = $block->getForm();
            //create new custom fieldset 'website'
            $fieldset = $form->addFieldset('website_field', array(
                    'legend' => 'Category Assigned to Users',
                    'class' => 'fieldset-wide'
                )
            );

            $fieldset->addField('view_categories', 'multiselect', array(
                'name'      => 'view_categories[]',
                'label'     => Mage::helper('adminhtml')->__('View Category'),
                'title'     => Mage::helper('adminhtml')->__('View Category'),
                'disabled'  => false,                    
                'index'     => 'view_categories',
                'value'     => Mage::helper('customconfig')->getSelectedCategory($user_id),
                'values'    => Mage::helper('customconfig')->getCustomCategory(),                   
            ));

        }

        if ($block->getType() == 'adminhtml/permissions_user_edit_form' && $role_data['role_id'] == Mage::getStoreConfig('customconfig_options/section_two/pos_user')) {
            $form = $block->getForm();
            //create new custom fieldset 'website'
            $fieldset = $form->addFieldset('website_field', array(
                    'legend' => 'Assign Pos User to Admin User',
                    'class' => 'fieldset-wide'
                )
            );

            $fieldset->addField('pos_user', 'select', array(
                'name'      => 'pos_user',
                'label'     => Mage::helper('adminhtml')->__('Pos User'),
                'title'     => Mage::helper('adminhtml')->__('Pos User'),
                'class'     => 'input-select',
                'required'     => true,               
                'index'     => 'pos_user',
                'value'     => Mage::helper('customconfig')->getSelectedWebposStaffId($user_id),
                'values'    => Mage::helper('customconfig')->getWebposStaffIds(),  

            ));
        }

        if ($block->getType() == 'adminhtml/permissions_user_edit_form') {
            $userdata = Mage::getModel('customconfig/usercategory')->load($user_id, 'user_id');
            $form = $block->getForm();
            $fieldset = $form->addFieldset('order_status_report', array(
                    'legend' => 'Assign Permission for Order Status Report',
                    'class' => 'fieldset-wide'
                )
            );

            // $fieldset->addField('allow_store_switcher', 'select', array(              
            //       'name'      => 'allow_store_switcher',
            //       'label'     => Mage::helper('adminhtml')->__('Show Store Switcher'),
            //       'title'     => Mage::helper('adminhtml')->__('Show Store Switcher'),
            //       'class'     => 'required-entry',
            //       'value'     => $userdata->getAllowStoreSwitcher(),
            //       'values'    => array('1'=>'Yes','0' => 'No'),
            // ));

            $fieldset->addField('allow_order_count', 'select', array(              
                  'name'      => 'allow_order_count',
                  'label'     => Mage::helper('adminhtml')->__('Show Order Count '),
                  'title'     => Mage::helper('adminhtml')->__('Show Order Count '),
                  'class'     => 'required-entry',
                  'value'     => $userdata->getAllowOrderCount(),
                  'values'    => array('1'=>'Yes','0' => 'No'),
            ));

            $fieldset->addField('allow_order_total', 'select', array(              
                  'name'      => 'allow_order_total',
                  'label'     => Mage::helper('adminhtml')->__('Show Order Total '),
                  'title'     => Mage::helper('adminhtml')->__('Show Order Total '),
                  'class'     => 'required-entry',
                  'value'     => $userdata->getAllowOrderTotal(),
                  'values'    => array('1'=>'Yes','0' => 'No'),
            ));

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('store_id', 'multiselect', array(
                    'name'      => 'stores[]',
                    'label'     => Mage::helper('adminhtml')->__('Allow Stores'),
                    'title'     => Mage::helper('adminhtml')->__('Allow Stores'),
                    'value'     => explode(',',$userdata->getAllowStoreId()),
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                ));
            } else {
                $fieldset->addField('store_id', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId(),
                ));
            }
        }

        if ($block->getType() == 'adminhtml/permissions_tab_roleinfo') {
            $form = $block->getForm();
            //create new custom fieldset 'website'
             $fieldset = $form->addFieldset('website_field', array(
                    'legend' => 'User Role Restrictions',
                    'class' => 'fieldset-wide'
                )
            );

            if ($role_data['role_id'] != Mage::getStoreConfig('customconfig_options/section_two/operation')){
                $disabled = true;
            }else{
                $disabled = false;
            }

            //add new website field
            $fieldset->addField('viewd_status', 'multiselect', array(
                'name'      => 'viewd_status[]',
                'label'     => Mage::helper('adminhtml')->__('View Status'),
                'title'     => Mage::helper('adminhtml')->__('View Status'),
                'disabled'  => $disabled,                    
                'index'     => 'viewd_status',
                'value'     => Mage::helper('customconfig')->getSelectViewedStatus(Mage::app()->getRequest()->getParam('rid')),
                'values'    => Mage::helper('customconfig')->getCustomStatus(),                   
            ));

            $fieldset->addField('allowed_status', 'multiselect', array(
                'name'      => 'allowed_status[]',
                'label'     => Mage::helper('adminhtml')->__('Allow Status'),
                'title'     => Mage::helper('adminhtml')->__('Allow Status'),
                'disabled'  => $disabled,
                'index'     => 'allowed_status',
                'value'     => Mage::helper('customconfig')->getSelectAllowedStatus(Mage::app()->getRequest()->getParam('rid')),
                'values'    => Mage::helper('customconfig')->getCustomStatus(),
            ));
        }
    }

    public function saveUserRoleData(Varien_Event_Observer $observer)
    {
        $observer->getEvent();
        $data = $observer->getEvent()->getRequest();
        $role_id = $data->getParam('role_id');
        $rolename = $data->getParam('rolename');
        $viewd_status = implode(",",$data->getParam('viewd_status'));
        $allowed_status = implode(",",$data->getParam('allowed_status'));
        
        $getCollections = Mage::getModel('customconfig/customconfig')->getCollection()->addFieldToFilter('role_id',$role_id);
        

        if($getCollections->getData()){
            $getCollection = Mage::getModel('customconfig/customconfig')->load($role_id, 'role_id');     
        }else{
            $getCollection = Mage::getModel('customconfig/customconfig');            
        }
        $getCollection->setRoleId($role_id);
        $getCollection->setRolename($rolename);
        $getCollection->setViewdStatus($viewd_status);
        $getCollection->setAllowedStatus($allowed_status);                
        $getCollection->save();       
    }


    /*******saveCategoriesToUser******/
    public function saveCategoriesToUser(Varien_Event_Observer $observer)
    {
        $observer->getEvent();
        $data = $observer->getEvent()->getObject();
        $view_categories = $data->getData('view_categories');
        $admin_user_session = Mage::getSingleton('admin/session');
        $user_id = $data->getData('user_id');
        $role_data = Mage::getModel('admin/user')->load($user_id)->getRole()->getData();
        $UserRoles = $data->getData('user_roles'); //$role_data['user_roles'];
        //$PosUser = NULL;
        //if($data->getData('pos_user')){
            //$PosUser = $data->getData('pos_user');
        //}
        $CatIds = null;
        if($view_categories){
           $CatIds = implode(",", $view_categories); 
        }
        //$CatIds = implode(",", $view_categories);
        $RoleId = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        $Rolename = $role_data['role_name'];        
        $getCollections = Mage::getModel('customconfig/usercategory')->getCollection()->addFieldToFilter('user_id',$user_id);
        if($getCollections->getData()){
            $getCollection = Mage::getModel('customconfig/usercategory')->load($user_id, 'user_id');
        }else{
            $getCollection = Mage::getModel('customconfig/usercategory');
        }
        $getCollection->setUserId($user_id);
        $getCollection->setUserRoles($UserRoles);
        $getCollection->setCatIds($CatIds);
        $getCollection->setRolename($Rolename); 
        if($data->getData('pos_user')){
            $getCollection->setPosUser($data->getData('pos_user'));
        }
        //$getCollection->setAllowStoreSwitcher($data->getData('allow_store_switcher'));
        $getCollection->setAllowOrderCount($data->getData('allow_order_count'));
        $getCollection->setAllowOrderTotal($data->getData('allow_order_total'));
        if($data->getData('stores')) {
            if( in_array('0', $data->getData('stores')) ){
                $store_ids = '0';
            } else {
                $store_ids = join(",", $data->getData('stores'));
            }
            $getCollection->setAllowStoreId($store_ids);
        }
        $getCollection->save();    
    }

    public function deleteCategoriesToUser(Varien_Event_Observer $observer)
    {
        $observer->getEvent();
        $data = $observer->getEvent()->getObject();
        $user_id = $data->getData('user_id');
        $getCollections = Mage::getModel('customconfig/usercategory')->getCollection()->addFieldToFilter('user_id',$user_id);
        if($getCollections->getData()){
            $category_data = Mage::getModel('customconfig/usercategory')->load($user_id, 'user_id');
            $category_data->delete();
        }
    }

    public function quoteItemSetProductCategory($observer) {
        /***** Start update quote item with category id *****/
        $getQuoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();
        $product = Mage::getModel('catalog/product')->load($product->getData('entity_id'));
        //$getCategoryIds = implode(',',$product->getCategoryIds());
        $getCategoryIds = $product->getCategoryIds();

        if ($getCategoryIds) {
            $getQuoteItem->setCategoryId(implode(',', $getCategoryIds));
            /*$category_id = array_rand($getCategoryIds);
            $Cat_Id = $getCategoryIds[$category_id];
            $getQuoteItem->setCategoryId($Cat_Id);*/

            /*$RoleId = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
            $usercategory = Mage::getModel('customconfig/usercategory')->getCollection()
            ->addFieldToFilter('cat_ids', $Cat_Id)
            ->addFieldToFilter('user_roles', $RoleId);
            $user_array = array();
            foreach($usercategory as $Users){
                $user_array[] = $Users['user_id'];
            }
            $UserId = array_rand($user_array);
            $quote = Mage::getSingleton('checkout/session')->getQuote();       
            //$quote = Mage::getModel('sales/quote')->load($getQuoteItem->getQuoteId(), 'entity_id');
            $quote->setKitchenUserIds($user_array[$UserId]);
            $quote->save();*/
        }        
        
        /***** End update quote item with category id *****/
    }

    public function orderItemSetProductCategory($observer) {
        /***** Start update order item with category id *****/        
        $data = $observer->getEvent()->getOrder();
        $collection = Mage::getModel('sales/quote_item')->getCollection()
                ->addFieldToFilter('quote_id', $data->getData('quote_id'));
        $category_ids = array();
        foreach($collection->getData() as $data_value){
            $category_ids[] = $data_value['category_id'];            
        }    
        //$category_id = array_rand($category_ids);
        //$data->setCategoryId($category_ids[$category_id]);
        $categoryid = array_unique(explode(',', implode(',', $category_ids)));
        $category_filter = array();
        foreach ($categoryid as $key => $value) {
            $category_filter[] = array('finset'=>array($value));
        }
        $RoleId = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        $usercategory = Mage::getModel('customconfig/usercategory')->getCollection()
        //->addFieldToFilter('cat_ids', array('like' => '%'.$category_ids[0].'%')) //$category_ids[$category_id]
        ->addFieldToFilter('cat_ids', $category_filter)
        ->addFieldToFilter('user_roles', $RoleId);
        $user_array = array();
        foreach($usercategory as $Users){
            $user_array[] = $Users['user_id'];
        }
        //$UserId = array_rand($user_array); 
        //$quote = $data->getQuote();  //Mage::getSingleton('checkout/session')->getQuote();       
        //$quote->setKitchenUserIds($user_array[$UserId]);
        //$quote->setKitchenUserIds(implode(',', $user_array));
        //$quote->save();

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('sales/quote');
        $kitchen_users = implode(',', array_unique($user_array));
        $query = "UPDATE {$table} SET kitchen_user_ids = '{$kitchen_users}' WHERE entity_id = "
                 . (int) $data->getData('quote_id');
        $writeConnection->query($query);
        /***** End update order item with category id *****/
    }

    public function updateKitchenUseronOrder($observer) {
        //$order_id = $observer->getData('order_ids');
        //$order = Mage::getModel('sales/order')->load($order_id);
        $order = $observer->getEvent()->getOrder();
        //$itemObject = Mage::getModel('sales/quote_item')->load($order->getQuoteId(), 'quote_id');
        //$order_item = Mage::getModel('sales/order_item')->load($order_id, 'order_id');
        //$quote =  $observer->getEvent()->getQuote(); //Mage::getModel('sales/quote')->load($order->getQuoteId(), 'entity_id');
        //$cat_ids = $itemObject->getData('category_id');
        //$order_item->setCategoryId($cat_ids);
        //$order_item->save();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $quote_table = $resource->getTableName('sales/quote');
        $sql        = "SELECT `kitchen_user_ids` FROM {$quote_table} WHERE `entity_id`=".(int) $order->getQuoteId();
        $rows       = $readConnection->fetchAll($sql);
        $kitchen_users = $rows[0]['kitchen_user_ids'];
        $order->setKitchenUserIds($kitchen_users);
        //Mage::log($order->getQuoteId(), null, 'order.log'); 
        $order->save();

        /*$resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');
        $quote_table = $resource->getTableName('sales/quote');
        $order_table = $resource->getTableName('sales/order');
        $sql        = "SELECT `kitchen_user_ids` FROM {$quote_table} WHERE `entity_id`=".(int) $order->getQuoteId();
        $rows       = $readConnection->fetchAll($sql);
        $kitchen_users = $rows[0]['kitchen_user_ids'];
        $query = "UPDATE {$order_table} SET kitchen_user_ids = '{$kitchen_users}' WHERE entity_id = "
                 . (int) $order->getId();
        $writeConnection->query($query);*/

        /*$custom_email_template = Mage::getStoreConfig('customconfig_options/section_three/emailtemplate');
        $email = Mage::getStoreConfig('customconfig_options/section_three/email');
        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault($custom_email_template);
        $emailTemplateVariables = $order->getData();
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $emailTemplate->send($email,'', $emailTemplateVariables);*/
    }

    public function sendDeliveryDelayEmail()
    {
        $orderCollection = Mage::getResourceModel('sales/order_collection');
        $orderCollection->addFieldToFilter('order_status', array(
            array('name'=>'order_status','neq'=>'canceled'),
            array('name'=>'order_status','neq'=>'delivered'), 
            array('name'=>'order_status','neq'=>'returned'),            
        ));
        // $orderCollection->addFieldToFilter('webpos_delivery_date', array(
        //                             'gt' =>  new Zend_Db_Expr("DATE_ADD('".now()."', INTERVAL -'15:00' HOUR_MINUTE)")));
        $orderCollection->addFieldToFilter('shipping_delivery_date', array(
                                    'gt' =>  new Zend_Db_Expr("DATE_ADD('".now()."', INTERVAL -'15:00' HOUR_MINUTE)")));
        $orders ="";
        foreach($orderCollection->getItems() as $order)
        {
            $orderModel = Mage::getModel('sales/order');
            $orderModel->load($order['entity_id']);
            $custom_email_template = Mage::getStoreConfig('customconfig_options/section_delivery/emailtemplate');
            $email_array = explode(',',Mage::getStoreConfig('customconfig_options/section_delivery/email')); 
            $emailTemplate  = Mage::getModel('core/email_template')->loadDefault($custom_email_template);
            $emailTemplateVariables = $orderModel->getData();
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
            $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
            $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
            foreach($email_array as $email){
                $emailTemplate->send($email, $receiveName, $emailTemplateVariables);
            }    
        }
        return $this;        
    }

    public function checkoutSubmitAllAfter2($observer){
        $data = $observer->getEvent()->getOrder();        
        //Mage::log($data->getCustomer()->getPrimaryBillingAddress()->getTelephone(), null, 'mylog3.log');
        if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
            
            if($telephone = $data->getBillingAddress()->getTelephone()){
                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');

                $order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($data->getIncrementId());        
                $order_url = Mage::getUrl($order_urls, array('_secure' => true));
                Mage::log($order_url , null, 'mylog3.log');
                $fields = array(
                    'username' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname'),
                    'password' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd'),
                    'customerid' => '361',
                    'sendertext' => 'HiNet GCC',
                    'message' => 'Unicode Message sended',
                    'dca'      => '16bit',
                    'msisdn' => '96560056516',
                    'messagebody' => str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/ordermessage')),
                    'recipientnumbers' => '965'.$telephone, // '96550618808',                    'defdate' => '', //Mage::getModel('core/date')->date('Y-m-d'),
                    'isblink' => 'false',
                    'isflash' => 'false'
                );
                $field = http_build_query($fields); // encode array to POST string
                //Mage::log(print_r($field, 1), null, 'mylog3.log');    
            
                $post = curl_init();
                curl_setopt($post, CURLOPT_URL, $url);
                curl_setopt($post, CURLOPT_POST, 1);
                curl_setopt($post, CURLOPT_POSTFIELDS, $field);
                curl_setopt($post, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($post); 
                // for debugging
                Mage::log($post, null, 'll.log');   
                
                Mage::log($result, null, 'll.log');   
                $sxml = new SimpleXMLElement($result);

                Mage::log($sxml, null, 'll.log');   
                $Result = $this->xml2array($sxml);
                Mage::log('dd2', null, 'll.log');  
                if($Result['Result'] == 'true'){
                    $data->setIsCustomerNotify('1');
                }else{            
                    $data->setIsCustomerNotify('0');
                }
                curl_close($post);
                Mage::log(print_r($result, 1) , null, 'mylog3.log');
            }      
        } 


    }
    public function checkoutSubmitAllAfter($observer){
        Mage::log('1', null, 'll.log');
        $data = $observer->getEvent()->getOrder();        
        Mage::log($data->getBillingAddress()->getTelephone(), null, 'll.log'); 
    
        if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) 
        {
            if($telephone = $data->getBillingAddress()->getTelephone()){
                 
                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');
                $order_urls = 'sales/guest/view/order_id/'.Mage::helper('core')->urlEncode($data->getIncrementId());        
                $order_url = Mage::getUrl($order_urls, array('_secure' => true));
                $username = Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname');
                $password = Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd');
                $msisdn = '965'.$telephone;
                $unicode_msg = str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/ordermessage'));
                $post_body = $this->seven_bit_sms( $username, $password, $unicode_msg, $msisdn );
                Mage::log($unicode_msg, null, 'll.log'); 
                $result =  $this->send_message( $post_body, $url );
                Mage::log($result, null, 'll.log'); 
                if($result['success']){
                    $data->setIsCustomerNotify('1');
                }else{            
                    $data->setIsCustomerNotify('0');
                }
              
            }      
        }     
    }

    public function checkoutSubmitAllAfterCron($observer){
        if(Mage::getStoreConfig('customconfig_options/section_smsconfig/active')) {
            $orderCollection = Mage::getResourceModel('sales/order_collection');
            $orderCollection->addFieldToFilter('is_customer_notify', array(
                array('name'=>'is_customer_notify','eq'=>'0')           
            ));
            foreach($orderCollection as $order){
                $orderModel = Mage::getModel('sales/order')->load($order->getEntityId());
                
                $url = Mage::getStoreConfig('customconfig_options/section_smsconfig/smsconfigurl');        

                $fields = array(
                    'username' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apiuname'),
                    'password' => Mage::getStoreConfig('customconfig_options/section_smsconfig/apipwd'),
                    'customerid' => '361',
                    'sendertext' => 'HiNet GCC',
                    'messagebody' => str_replace('{{order link}}', $order_url, Mage::getStoreConfig('customconfig_options/section_smsconfig/ordermessage')),
                    'recipientnumbers' => '00201008073788',
                    'defdate' => Mage::getModel('core/date')->date('Y-m-d'),
                    'isblink' => 'false',
                    'isflash' => 'false'
                );
                $field = http_build_query($fields); // encode array to POST string        
                $post = curl_init();
                curl_setopt($post, CURLOPT_URL, $url);
                curl_setopt($post, CURLOPT_POST, 1);
                curl_setopt($post, CURLOPT_POSTFIELDS, $field);
                curl_setopt($post, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($post);        
                // for debugging
                Mage::log($result, null, 'll.log');  
                $sxml = new SimpleXMLElement($result);
                $Result = $this->xml2array($sxml);   
                Mage::log('dd2', null, 'll.log');           
                if($Result['Result'] == 'true'){
                    $orderModel->setIsCustomerNotify('1');
                }else{
                    $orderModel->setIsCustomerNotify('0');
                }
                $orderModel->save();
                curl_close($post);
                Mage::log(print_r($result, 1), null, 'mylog4.log');
            }
        }
    }

    public function xml2array($xml){
        $arr = array();

        foreach ($xml as $element)
        {
            $tag = $element->getName();
            $e = get_object_vars($element);
            if (!empty($e))
            {
                $arr[$tag] = $element instanceof SimpleXMLElement ? xml2array($element) : $e;
            }
            else
            {
                $arr[$tag] = trim($element);
            }
        }

        return $arr;
    }
    
    public function catalogProductEditAction($observer){
        $event = $observer->getEvent();
        $product = $event->getProduct();       
        $product->lockAttribute('totalcost');
    }
    
    /*public function catalogProductLoadAfter($observer){
        $event = $observer->getEvent();
        $product = $event->getProduct();       
        $_product = Mage::getModel('catalog/product')->load($product->getId()); 
        echo $TotalCost = $_product->getCost() + ($_product->getCost() * $_product->getOverhead())/100;        
        if($TotalCost){            
            $_product->setTotalcost($TotalCost);
            $_product->save();
        }
    } */

    public function catalogProductLoadAfter($observer){
        $product = $observer->getProduct();  
        $product = Mage::getModel('catalog/product')->load($product->getId()); 
        
        $TotalCost = $product->getCost() + ($product->getCost() * $product->getOverhead())/100;        
        if($TotalCost){            
            $product->setTotalcost($TotalCost);
            $product->getResource()->saveAttribute($product, 'totalcost');
        }
    }

    public function addOptionsToOrderItem($observer){        

        $orderItem = $observer->getOrderItem();
        $options = $orderItem->getProductOptions(); 
        $customOptions = $options['options'];   
        $options = '';
        $TextFieldoptions = '';
        if(!empty($customOptions))
        {
            // foreach ($customOptions as $option)
            // {        
            //     $options = $options.$option['label'].','.$option['option_id'].','.$option['value'].';';
            // }
            // $orderItem->setCustomOptions($options);

            foreach ($customOptions as $option)
            {        
                $options = $options.$option['label'].','.$option['option_id'].','.$option['value'].';';
                if($option['option_type'] == 'field'){
                    $TextFieldoptions = $TextFieldoptions.$option['label'].':'.$option['value'].';';
                }
            }
            $orderItem->setCustomOptions($options);           
            if($TextFieldoptions){
                $orderItem->setIsTextCustomOption(true)->setTextCustomOptionsValue($TextFieldoptions);
            }
        }
    }
    
    public function adminhtmlWidgetContainerHtmlBefore($event)
    {
        $block = $event->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $block->removeButton('order_cancel');
            $block->removeButton('order_ship');
            $block->removeButton('order_invoice');
            $block->removeButton('order_creditmemo');
            $block->removeButton('order_hold');
        }
        if ($block instanceof Cmsmart_AdminTheme_Block_Adminhtml_Block_Sales_Order_View){
            $block->removeButton('order_cancel');
            $block->removeButton('order_ship');
            $block->removeButton('order_invoice');
            $block->removeButton('order_creditmemo');
            $block->removeButton('order_hold');
        }
    }

    public function updateStoreView($observer){
        $product            = $observer->getProduct();
        $EnglishStores      = array(1,4,5);
        $ArabicStores       = array(7,8,9);
        $update_attributes  = array('name','description','short_description');
        $update_attributes_values = array();
        $revert_attributes_values = array();
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $eavConfig  = Mage::getModel('eav/config');
        $tables     = array();
        foreach ($update_attributes as $key => $value) {
            if($product->getData($value)){
                $update_attributes_values[$value] = $product->getData($value);
            }
            if($product->getData($value) === false){
                $revert_attributes_values[] = $value;
            }
        }        
        foreach ($revert_attributes_values as $attributeCode){
            $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
            if ($attribute){
                $tableName = $resource->getTableName('catalog/product') . '_' . $attribute->getBackendType();
                $tables[$tableName][] = $attribute->getId();
            }
        }
        // For English Stores
        if($product->getData('store_id') != 0 && in_array($product->getData('store_id'), $EnglishStores)){           
            if(count($update_attributes_values)){
                foreach ($EnglishStores as $key => $storeId) {
                    if($product->getData('store_id') != $storeId){
                        Mage::getSingleton('catalog/product_action')->updateAttributes(
                            array($product->getId()),
                            $update_attributes_values,
                            $storeId
                        );
                    }        
                }
            }
            if(count($revert_attributes_values)){
                foreach ($EnglishStores as $key => $storeId) {
                    if($product->getData('store_id') != $storeId){
                        foreach ($tables as $tableName => $attributeIds){
                            $attributeIdsAsString = implode(',', $attributeIds);
                            $q = "DELETE FROM {$tableName}
                                        WHERE
                                            attribute_id IN ({$attributeIdsAsString}) AND
                                            entity_id IN ({$product->getId()}) AND
                                            store_id IN ({$storeId})";
                            $connection->query($q);
                        }
                    }                                    
                }
            }                 
        }
        // For Arabic Stores
        if($product->getData('store_id') != 0 && in_array($product->getData('store_id'), $ArabicStores)){           
            if(count($update_attributes_values)){
                foreach ($ArabicStores as $key => $storeId) {
                    if($product->getData('store_id') != $storeId){
                        Mage::getSingleton('catalog/product_action')->updateAttributes(
                            array($product->getId()),
                            $update_attributes_values,
                            $storeId
                        );
                    }        
                }
            }
            if(count($revert_attributes_values)){
                foreach ($ArabicStores as $key => $storeId) {
                    if($product->getData('store_id') != $storeId){
                        foreach ($tables as $tableName => $attributeIds){
                            $attributeIdsAsString = implode(',', $attributeIds);
                            $q = "DELETE FROM {$tableName}
                                        WHERE
                                            attribute_id IN ({$attributeIdsAsString}) AND
                                            entity_id IN ({$product->getId()}) AND
                                            store_id IN ({$storeId})";
                            $connection->query($q);
                        }
                    }                                    
                }
            }                 
        }
    }
    /*Edit by 24122017 Islam ELgarhy*/

    public function convertyQuotaAttrToOrder($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        $orderItem = $observer->getOrderItem();
        $discount_cause =$quoteItem->getdiscount_cause();
        if ($discount_cause != "" && $discount_cause != NULL) {
         
            $orderItem->setdiscount_cause($discount_cause);
            
        }

        $discount_by =$quoteItem->getdiscount_by();
        if ($discount_by != "" && $discount_by != NULL) {
         
            $orderItem->setdiscount_by($discount_by);
            
        }

        return $this;
    }
      /*Edit by 24122017 Islam ELgarhy*/



    // Test SMS Islam Elgarhy 


      function send_message ( $post_body, $url ) {
        /*
        * Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn it into in a
        * multipart formpost, which is not supported:
        */
      
        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
        // Allowing cUrl funtions 20 second to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Waiting 20 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
      
        $response_string = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );
      
        $sms_result = array();
        $sms_result['success'] = 0;
        $sms_result['details'] = '';
        $sms_result['transient_error'] = 0;
        $sms_result['http_status_code'] = $curl_info['http_code'];
        $sms_result['api_status_code'] = '';
        $sms_result['api_message'] = '';
        $sms_result['api_batch_id'] = '';
      
        if ( $response_string == FALSE ) {
          $sms_result['details'] .= "cURL error: " . curl_error( $ch ) . "\n";
        } elseif ( $curl_info[ 'http_code' ] != 200 ) {
          $sms_result['transient_error'] = 1;
          $sms_result['details'] .= "Error: non-200 HTTP status code: " . $curl_info[ 'http_code' ] . "\n";
        }
        else {
          $sms_result['details'] .= "Response from server: $response_string\n";
          $api_result = explode( '|', $response_string );
          $status_code = $api_result[0];
          $sms_result['api_status_code'] = $status_code;
          $sms_result['api_message'] = $api_result[1];
          if ( count( $api_result ) != 3 ) {
            $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count( $api_result );
          } else {
            if ($status_code == '0') {
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
              $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
            }
            else if ($status_code == '1') {
              # Success: scheduled for later sending.
              $sms_result['success'] = 1;
              $sms_result['api_batch_id'] = $api_result[2];
            }
            else {
              $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
            }
      
      
      
      
      
          }
        }
        curl_close( $ch );
      
        return $sms_result;
      }
      

      function seven_bit_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
        'username' => $username,
        'password' => $password,
        'message'  => $this->character_resolve( $message ),
        'msisdn'   => $msisdn,
        'allow_concat_text_sms' => 0, # Change to 1 to enable long messages
        'concat_text_sms_max_parts' => 2
        );
      
        return $this->make_post_body($post_fields);
      }

      function unicode_sms ( $username, $password, $message, $msisdn ) {
        $post_fields = array (
        'username' => $username,
        'password' => $password,
        'message'  => $this->string_to_utf16_hex( $message ),
        'msisdn'   => $msisdn,
        'dca'      => '16bit',
        'allow_concat_text_sms' => 1
        );
      
        return $this->make_post_body($post_fields);
      }

      function make_post_body($post_fields) {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
          $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach( $post_fields as $key => $value ) {
          $post_body .= urlencode( $key ).'='.urlencode( $value ).'&';
        }
        $post_body = rtrim( $post_body,'&' );
      
        return $post_body;
      }
      
  
      
      /*
      * Unique ID to eliminate duplicates in case of network timeouts - see
      * EAPI documentation for more. You may want to use a database primary
      * key. Warning: sending two different messages with the same
      * ID will result in the second being ignored!
      *
      * Don't use a timestamp - for instance, your application may be able
      * to generate multiple messages with the same ID within a second, or
      * part thereof.
      *
      * You can't simply use an incrementing counter, if there's a chance that
      * the counter will be reset.
      */
      function make_stop_dup_id() {
        return 0;
      }
      
      function string_to_utf16_hex( $string ) {
        return bin2hex(mb_convert_encoding($string, "UTF-16", "UTF-8"));
      }
      
      function character_resolve($body) {
        $special_chrs = array(
        'Δ'=>'0xD0', 'Φ'=>'0xDE', 'Γ'=>'0xAC', 'Λ'=>'0xC2', 'Ω'=>'0xDB',
        'Π'=>'0xBA', 'Ψ'=>'0xDD', 'Σ'=>'0xCA', 'Θ'=>'0xD4', 'Ξ'=>'0xB1',
        '¡'=>'0xA1', '£'=>'0xA3', '¤'=>'0xA4', '¥'=>'0xA5', '§'=>'0xA7',
        '¿'=>'0xBF', 'Ä'=>'0xC4', 'Å'=>'0xC5', 'Æ'=>'0xC6', 'Ç'=>'0xC7',
        'É'=>'0xC9', 'Ñ'=>'0xD1', 'Ö'=>'0xD6', 'Ø'=>'0xD8', 'Ü'=>'0xDC',
        'ß'=>'0xDF', 'à'=>'0xE0', 'ä'=>'0xE4', 'å'=>'0xE5', 'æ'=>'0xE6',
        'è'=>'0xE8', 'é'=>'0xE9', 'ì'=>'0xEC', 'ñ'=>'0xF1', 'ò'=>'0xF2',
        'ö'=>'0xF6', 'ø'=>'0xF8', 'ù'=>'0xF9', 'ü'=>'0xFC',
        );
      
        $ret_msg = '';
        if( mb_detect_encoding($body, 'UTF-8') != 'UTF-8' ) {
          $body = utf8_encode($body);
        }
        for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
          $c = mb_substr( $body, $i, 1, 'UTF-8' );
          if( isset( $special_chrs[ $c ] ) ) {
            $ret_msg .= chr( $special_chrs[ $c ] );
          }
          else {
            $ret_msg .= $c;
          }
        }
        return $ret_msg;
      }
     

    // End Test SMS 
}
?>
