<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Ordermanage_Block_Sales_Order_Grid
    extends Iksanika_Ordermanage_Block_Widget_Grid
//    extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    public static $columnSettings = array();


    protected static $columnType = array(
        'id'                    =>  array('type'=>'number'),
        'order_ids'             =>  array('type'=>'checkbox'),

        'status'                =>  array('type'=>'options'),

//        'name'                  =>  array('type'=>'text', 'title' => 'Name', 'filter_index' => 'group_concat(`sales/order_item`.name SEPARATOR ",")'),
//        'sku'                   =>  array('type'=>'text', 'title' => 'Sku', 'filter_index' => 'group_concat(`sales/order_item`.sku SEPARATOR ",")'),
        'name'                  =>  array('type'=>'text', 'title' => 'Name', 'filter_index' => 'name'),
        'sku'                   =>  array('type'=>'text', 'title' => 'Sku', 'filter_index' => 'sku'),

        'billing_full_form'     =>  array('type'=>'text', 'title' => 'Billing Address', 'filter_index' => 'CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname, soa_billing.middlename, soa_billing.company, soa_billing.street, soa_billing.city, soa_billing.region, soa_billing.postcode, soa_billing.email, soa_billing.telephone, soa_billing.fax)'),
        'billing_firstname'     =>  array('type'=>'input', 'title' => 'Billing Firstname', 'filter_index' => 'soa_billing.firstname'),
        'billing_middlename'    =>  array('type'=>'input', 'title' => 'Billing Middlename', 'filter_index' => 'soa_billing.middlename'),
        'billing_lastname'      =>  array('type'=>'input', 'title' => 'Billing Lastname', 'filter_index' => 'soa_billing.lastname'),
        'billing_company'       =>  array('type'=>'input', 'title' => 'Billing Company', 'filter_index' => 'soa_billing.company'),
        'billing_street'        =>  array('type'=>'input', 'title' => 'Billing Street', 'filter_index' => 'soa_billing.street'),
        'billing_city'          =>  array('type'=>'input', 'title' => 'Billing City', 'filter_index' => 'soa_billing.city'),
        'billing_region'        =>  array('type'=>'input', 'title' => 'Billing Region', 'filter_index' => 'soa_billing.region'),
        'billing_postcode'      =>  array('type'=>'input', 'title' => 'Billing Postcode', 'filter_index' => 'soa_billing.postcode'),
        'billing_email'         =>  array('type'=>'input', 'title' => 'Billing Email', 'filter_index' => 'soa_billing.email'),
        'billing_telephone'     =>  array('type'=>'input', 'title' => 'Billing Telephone', 'filter_index' => 'soa_billing.telephone'),
        'billing_country'       =>  array('type'=>'country', 'title' => 'Billing Country', 'filter_index' => 'soa_billing.country_id'),
        'billing_fax'           =>  array('type'=>'input', 'title' => 'Billing Fax', 'filter_index' => 'soa_billing.fax'),

        'shipping_full_form'    =>  array('type'=>'text', 'title' => 'Shipping Address', 'filter_index' => 'CONCAT_WS(\' \', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax)'),
        'shipping_firstname'    =>  array('type'=>'input', 'title' => 'Shipping Firstname', 'filter_index' => 'soa_shippment.firstname'),
        'shipping_middlename'   =>  array('type'=>'input', 'title' => 'Shipping Middlename', 'filter_index' => 'soa_shippment.middlename'),
        'shipping_lastname'     =>  array('type'=>'input', 'title' => 'Shipping Lastname', 'filter_index' => 'soa_shippment.lastname'),
        'shipping_company'      =>  array('type'=>'input', 'title' => 'Shipping Company', 'filter_index' => 'soa_shippment.company'),
        'shipping_street'       =>  array('type'=>'input', 'title' => 'Shipping Street', 'filter_index' => 'soa_shippment.street'),
        'shipping_city'         =>  array('type'=>'input', 'title' => 'Shipping City', 'filter_index' => 'soa_shippment.city'),
        'shipping_region'       =>  array('type'=>'input', 'title' => 'Shipping Region', 'filter_index' => 'soa_shippment.region'),
        'shipping_postcode'     =>  array('type'=>'input', 'title' => 'Shipping Postcode', 'filter_index' => 'soa_shippment.postcode'),
        'shipping_email'        =>  array('type'=>'input', 'title' => 'Shipping Email', 'filter_index' => 'soa_shippment.email'),
        'shipping_telephone'    =>  array('type'=>'input', 'title' => 'Shipping Telephone', 'filter_index' => 'soa_shippment.telephone'),
        'shipping_country'      =>  array('type'=>'country', 'title' => 'Shipping Country', 'filter_index' => 'soa_shippment.country_id'),
        'shipping_fax'          =>  array('type'=>'input', 'title' => 'Shipping Fax', 'filter_index' => 'soa_shippment.fax'),
/*
        'state'                 =>      array('type' => 'text', 'title' => ''),
        'coupon_code'           =>      array('type' => 'text', 'title' => ''),
        'protect_code'          =>      array('type' => 'text', 'title' => ''),
        'shipping_description'  =>      array('type' => 'text', 'title' => ''),
        'is_virtual'            =>      array('type' => 'text', 'title' => ''),
        'customer_id'           =>      array('type' => 'text', 'title' => ''),
        'base_discount_amount'  =>      array('type' => 'text', 'title' => ''),
        'base_discount_canceled'=>      array('type' => 'text', 'title' => ''),
        'base_discount_invoiced'=>      array('type' => 'text', 'title' => ''),
        'base_discount_refunded'=>      array('type' => 'text', 'title' => ''),
        'base_grand_total'      =>      array('type' => 'text', 'title' => ''),
        'base_shipping_amount'  =>      array('type' => 'text', 'title' => ''),
        'base_shipping_canceled'=>      array('type' => 'text', 'title' => ''),
        'base_shipping_invoiced'=>      array('type' => 'text', 'title' => ''),
        'base_shipping_refunded'=>      array('type' => 'text', 'title' => ''),
        'base_shipping_tax_amount'  =>      array('type' => 'text', 'title' => ''),
        'base_shipping_tax_refunded'=>      array('type' => 'text', 'title' => ''),
        'base_subtotal'             =>      array('type' => 'text', 'title' => ''),
        'base_subtotal_canceled'=>      array('type' => 'text', 'title' => ''),
        'base_subtotal_invoiced'=>      array('type' => 'text', 'title' => ''),
        'base_subtotal_refunded'=>      array('type' => 'text', 'title' => ''),
        'base_tax_amount'       =>      array('type' => 'text', 'title' => ''),
        'base_tax_canceled'     =>      array('type' => 'text', 'title' => ''),
        'base_tax_invoiced'     =>      array('type' => 'text', 'title' => ''),
        'base_tax_refunded'     =>      array('type' => 'text', 'title' => ''),
        'base_to_global_rate'   =>      array('type' => 'text', 'title' => ''),
        'base_to_order_rate'    =>      array('type' => 'text', 'title' => ''),
        'base_total_canceled'   =>      array('type' => 'text', 'title' => ''),
        'base_total_invoiced'   =>      array('type' => 'text', 'title' => ''),
        'base_total_invoiced_cost'      =>      array('type' => 'text', 'title' => ''),
        'base_total_offline_refunded'   =>      array('type' => 'text', 'title' => ''),
        'base_total_online_refunded'    =>      array('type' => 'text', 'title' => ''),
        'base_total_paid'               =>      array('type' => 'text', 'title' => ''),
        'base_total_qty_ordered'        =>      array('type' => 'text', 'title' => ''),
        'base_total_refunded'           =>      array('type' => 'text', 'title' => ''),
        'discount_amount'               =>      array('type' => 'text', 'title' => ''),
        'discount_canceled'             =>      array('type' => 'text', 'title' => ''),
        'discount_invoiced'             =>      array('type' => 'text', 'title' => ''),
        'discount_refunded'             =>      array('type' => 'text', 'title' => ''),
        'grand_total'                   =>      array('type' => 'text', 'title' => ''),
        'shipping_amount'               =>      array('type' => 'text', 'title' => ''),
        'shipping_canceled'             =>      array('type' => 'text', 'title' => ''),
        'shipping_invoiced'             =>      array('type' => 'text', 'title' => ''),
        'shipping_refunded'             =>      array('type' => 'text', 'title' => ''),
        'shipping_tax_amount'       =>      array('type' => 'text', 'title' => ''),
        'shipping_tax_refunded'     =>      array('type' => 'text', 'title' => ''),
        'store_to_base_rate'        =>      array('type' => 'text', 'title' => ''),
        'store_to_order_rate'       =>      array('type' => 'text', 'title' => ''),
        'subtotal'                  =>      array('type' => 'text', 'title' => ''),
        'subtotal_canceled'         =>      array('type' => 'text', 'title' => ''),
        'subtotal_invoiced'         =>      array('type' => 'text', 'title' => ''),
        'subtotal_refunded'         =>      array('type' => 'text', 'title' => ''),
        'tax_amount'                =>      array('type' => 'text', 'title' => ''),
        'tax_canceled'              =>      array('type' => 'text', 'title' => ''),
        'tax_invoiced'              =>      array('type' => 'text', 'title' => ''),
        'tax_refunded'              =>      array('type' => 'text', 'title' => ''),
        'total_canceled'            =>      array('type' => 'text', 'title' => ''),
        'total_invoiced'            =>      array('type' => 'text', 'title' => ''),
        'total_offline_refunded'    =>      array('type' => 'text', 'title' => ''),
        'total_online_refunded'     =>      array('type' => 'text', 'title' => ''),
        'total_paid'                =>      array('type' => 'text', 'title' => ''),
        'total_qty_ordered'         =>      array('type' => 'text', 'title' => ''),
        'total_refunded'            =>      array('type' => 'text', 'title' => ''),
        'can_ship_partially'        =>      array('type' => 'text', 'title' => ''),
        'can_ship_partially_item'   =>      array('type' => 'text', 'title' => ''),
        'customer_is_guest'         =>      array('type' => 'text', 'title' => ''),
        'customer_note_notify'      =>      array('type' => 'text', 'title' => ''),
        'billing_address_id'        =>      array('type' => 'text', 'title' => ''),
        'customer_group_id'         =>      array('type' => 'text', 'title' => ''),
        'edit_increment'            =>      array('type' => 'text', 'title' => ''),
        'email_sent'                =>      array('type' => 'text', 'title' => ''),
        'forced_shipment_with_invoice'  =>      array('type' => 'text', 'title' => ''),
        'payment_auth_expiration'       =>      array('type' => 'text', 'title' => ''),
        'quote_address_id'              =>      array('type' => 'text', 'title' => ''),
        'quote_id'                      =>      array('type' => 'text', 'title' => ''),
        'shipping_address_id'           =>      array('type' => 'text', 'title' => ''),
        'adjustment_negative'           =>      array('type' => 'text', 'title' => ''),
        'adjustment_positive'           =>      array('type' => 'text', 'title' => ''),
        'base_adjustment_negative'      =>      array('type' => 'text', 'title' => ''),
        'base_adjustment_positive'      =>      array('type' => 'text', 'title' => ''),
        'base_shipping_discount_amount' =>      array('type' => 'text', 'title' => ''),
        'base_subtotal_incl_tax'        =>      array('type' => 'text', 'title' => ''),
        'base_total_due'                =>      array('type' => 'text', 'title' => ''),
        'payment_authorization_amount'  =>      array('type' => 'text', 'title' => ''),
        'shipping_discount_amount'      =>      array('type' => 'text', 'title' => ''),
        'subtotal_incl_tax'             =>      array('type' => 'text', 'title' => ''),
        'total_due'                 =>      array('type' => 'text', 'title' => ''),
        'weight'                    =>      array('type' => 'text', 'title' => ''),
        'customer_dob'              =>      array('type' => 'text', 'title' => ''),
        'increment_id'              =>      array('type' => 'text', 'title' => ''),
        'applied_rule_ids'          =>      array('type' => 'text', 'title' => ''),
        'base_currency_code'        =>      array('type' => 'text', 'title' => ''),
        'customer_email'            =>      array('type' => 'text', 'title' => ''),
        'customer_firstname'        =>      array('type' => 'text', 'title' => ''),
        'customer_lastname'         =>      array('type' => 'text', 'title' => ''),
        'customer_middlename'       =>      array('type' => 'text', 'title' => ''),
        'customer_prefix'           =>      array('type' => 'text', 'title' => ''),
        'customer_suffix'           =>      array('type' => 'text', 'title' => ''),
        'customer_taxvat'           =>      array('type' => 'text', 'title' => ''),
        'discount_description'      =>      array('type' => 'text', 'title' => ''),
        'ext_customer_id'           =>      array('type' => 'text', 'title' => ''),
        'ext_order_id'              =>      array('type' => 'text', 'title' => ''),
        'global_currency_code'      =>      array('type' => 'text', 'title' => ''),
        'hold_before_state'         =>      array('type' => 'text', 'title' => ''),
        'hold_before_status'        =>      array('type' => 'text', 'title' => ''),
        'order_currency_code'       =>      array('type' => 'text', 'title' => ''),
        'original_increment_id'     =>      array('type' => 'text', 'title' => ''),
        'relation_child_id'         =>      array('type' => 'text', 'title' => ''),
        'relation_child_real_id'    =>      array('type' => 'text', 'title' => ''),
        'relation_parent_id'        =>      array('type' => 'text', 'title' => ''),
        'relation_parent_real_id'           =>      array('type' => 'text', 'title' => ''),
        'remote_ip'                         =>      array('type' => 'text', 'title' => ''),
        'shipping_method'                   =>      array('type' => 'text', 'title' => ''),
        'store_currency_code'               =>      array('type' => 'text', 'title' => ''),
        'store_name'                        =>      array('type' => 'text', 'title' => ''),
        'x_forwarded_for'                   =>      array('type' => 'text', 'title' => ''),
        'customer_note'                     =>      array('type' => 'text', 'title' => ''),
        'created_at'                        =>      array('type' => 'text', 'title' => ''),
        'updated_at'                        =>      array('type' => 'text', 'title' => ''),
        'total_item_count'                  =>      array('type' => 'text', 'title' => ''),
        'customer_gender'                   =>      array('type' => 'text', 'title' => ''),
        'hidden_tax_amount'                 =>      array('type' => 'text', 'title' => ''),
        'base_hidden_tax_amount'            =>      array('type' => 'text', 'title' => ''),
        'shipping_hidden_tax_amount'        =>      array('type' => 'text', 'title' => ''),
        'base_shipping_hidden_tax_amnt'     =>      array('type' => 'text', 'title' => ''),
        'hidden_tax_invoiced'               =>      array('type' => 'text', 'title' => ''),
        'base_hidden_tax_invoiced'          =>      array('type' => 'text', 'title' => ''),
        'hidden_tax_refunded'               =>      array('type' => 'text', 'title' => ''),
        'base_hidden_tax_refunded'          =>      array('type' => 'text', 'title' => ''),
        'shipping_incl_tax'                 =>      array('type' => 'text', 'title' => ''),
        'base_shipping_incl_tax'            =>      array('type' => 'text', 'title' => ''),
        'coupon_rule_name'                  =>      array('type' => 'text', 'title' => ''),
        'paypal_ipn_customer_notified'      =>      array('type' => 'text', 'title' => ''),
        'gift_message_id'                   =>      array('type' => 'text', 'title' => ''),
        'rewards_discount_amount'           =>      array('type' => 'text', 'title' => ''),
        'rewards_base_discount_amount'      =>      array('type' => 'text', 'title' => ''),
        'rewards_discount_tax_amount'       =>      array('type' => 'text', 'title' => ''),
        'rewards_base_discount_tax_amount'  =>      array('type' => 'text', 'title' => ''),
      */
    );

    public function __construct()
    {
        parent::__construct();
        $this->prepareDefaults();
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setId('orderGrid');

        self::prepareColumnSettings();
        $this->setTemplate('iksanika/ordermanage/sales/order/grid.phtml');
        $this->setMassactionBlockName('ordermanage/widget_grid_massaction');
    }

    private function prepareDefaults()
    {
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_STANDARD)
        {
            $this->setDefaultLimit(Mage::getStoreConfig('ordermanage/columns/limit'));
            $this->setDefaultPage(Mage::getStoreConfig('ordermanage/columns/page'));
            $this->setDefaultSort(Mage::getStoreConfig('ordermanage/columns/sort'));
            $this->setDefaultDir(Mage::getStoreConfig('ordermanage/columns/dir'));
        }else
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_ORDER_ITEMS)
        {
            $this->setDefaultLimit(Mage::getStoreConfig('ordermanage/columns/limit'));
            $this->setDefaultPage(Mage::getStoreConfig('ordermanage/columns/page'));

            $this->setDefaultSort('date');
            $this->setDefaultDir('desc'); //asc
        }
    }

    public static function prepareColumnSettings()
    {
        $storeSettings = Mage::getStoreConfig('ordermanage/columns/showcolumns');
        $tempArr = explode(',', $storeSettings);

        foreach($tempArr as $showCol)
        {
            self::$columnSettings[trim($showCol)] = true;
        }
    }

    public static function getColumnSettings()
    {
        if(count(self::$columnSettings) == 0)
        {
            self::prepareColumnSettings();
        }
        return self::$columnSettings;
    }

    public static function getColumnForUpdate()
    {
        $fields = array('order_ids');

        if(count(self::getColumnSettings()))
        {
            foreach(self::getColumnSettings() as $columnId => $status)
            {
                if(isset(self::$columnType[$columnId]))
                {
                    if(
                        self::$columnType[$columnId]['type'] == 'input' ||
                        self::$columnType[$columnId]['type'] == 'price' ||
                        self::$columnType[$columnId]['type'] == 'number' ||
                        self::$columnType[$columnId]['type'] == 'options' ||
                        self::$columnType[$columnId]['type'] == 'country' ||
                        self::$columnType[$columnId]['type'] == 'date'
                      )
                    {
                        $fields[] = $columnId;
                    }
                }
            }
        }
        return $fields;
    }

    public function colIsVisible($code)
    {
        return isset(self::$columnSettings[$code]);
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_config_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('ordermanage')->__('Save Config'),
                    'onclick'   => 'doSaveConfig()',
/*                    'class'   => 'task'*/
                ))
        );

        return parent::_prepareLayout();
    }

    public function getSaveConfigButtonHtml()
    {
        return $this->getChildHtml('save_config_button');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _getCollectionClass()
    {
        //return 'sales/order_grid_collection';
        return 'sales/order_collection';
    }



    protected function _prepareCollection_Orders()
    {



        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addAttributeToSelect('*')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left');

        $collection->getSelect()->joinLeft(
            array('sog' => Mage::getConfig()->getTablePrefix().'sales_flat_order_grid'),
//            array('sog' => 'sales/order_grid_collection'),
            'main_table.entity_id = sog.entity_id',
            array(
                'sog.shipping_name',
                'sog.billing_name',
                'sog.order_status',
                'sog.kitchen_user_ids'
            )
        );
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $kitchen = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        $driver = Mage::getStoreConfig('customconfig_options/section_two/driver');
        $pos_user = Mage::getStoreConfig('customconfig_options/section_two/pos_user');
        $role_id = $role_data->getRoleId();
        if($role_id != 1){
            $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');
            if($userrole_status->getViewdStatus()){
                $role_status = explode(',', $userrole_status->getViewdStatus());
                if($userrole_status->getId()){
                    // $collection = $observer->getOrderGridCollection();
                    if ($collection){
                        $collection->addFieldToFilter('sog.order_status', array('in' => $role_status));
                        if($role_id == $kitchen)
                        {
                            $collection->addFieldToFilter('sog.kitchen_user_ids', array('finset' => $adminuserId));
                        }
                        else if($role_id == $driver)
                        {
                            $collection->addFieldToFilter('sog.driver_id', $adminuserId);
                        }
                        elseif ($role_id == $pos_user) {
                            $posuser = Mage::helper('customconfig')->getSelectedWebposStaffId($adminuserId);
                            if($posuser != '' || $posuser != NULL){
                                $collection->addFieldToFilter('main_table.webpos_staff_id', $posuser);
                            }
                        }
                    }

                }
            }
        }

//        $collection->getSelect()->joinLeft(array('sfo'=>'sales_flat_order'),'sfo.entity_id=main_table.entity_id',array('sfo.customer_email','sfo.weight','sfo.discount_description','sfo.increment_id','sfo.store_id','sfo.created_at','sfo.status','sfo.base_grand_total','sfo.grand_total')); // New

        if(
            $this->colIsVisible('shipping_full_form') ||
            $this->colIsVisible('shipping_name') ||
            $this->colIsVisible('shipping_firstname') ||
            $this->colIsVisible('shipping_lastname') ||
            $this->colIsVisible('shipping_middlename') ||
            $this->colIsVisible('shipping_company') ||
            $this->colIsVisible('shipping_street') ||
            $this->colIsVisible('shipping_city') ||
            $this->colIsVisible('shipping_region') ||
            $this->colIsVisible('shipping_postcode') ||
            $this->colIsVisible('shipping_email') ||
            $this->colIsVisible('shipping_telephone') ||
            $this->colIsVisible('shipping_country') ||
            $this->colIsVisible('shipping_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_shippment' => Mage::getConfig()->getTablePrefix().'sales_flat_order_address'),
                'main_table.entity_id = soa_shippment.parent_id AND soa_shippment.address_type="shipping"',
                array(
//                    'shipping_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_shipping.firstname, soa_shipping.lastname)'),
                    'shipping_full_form'    => new Zend_Db_Expr('CONCAT_WS(\' \', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax)'),
                    'shipping_firstname'    => 'soa_shippment.firstname',
                    'shipping_lastname'     => 'soa_shippment.lastname',
                    'shipping_middlename'   => 'soa_shippment.middlename',
                    'shipping_company'      => 'soa_shippment.company',
                    'shipping_street'       => 'soa_shippment.street',
                    'shipping_city'         => 'soa_shippment.city',
                    'shipping_region'       => 'soa_shippment.region',
                    'shipping_postcode'     => 'soa_shippment.postcode',
                    'shipping_email'        => 'soa_shippment.email',
                    'shipping_telephone'    => 'soa_shippment.telephone',
                    'shipping_country'      => 'soa_shippment.country_id',
                    'shipping_fax'          => 'soa_shippment.fax',
                )
            );
        }

        if(
            $this->colIsVisible('billing_full_form') ||
            $this->colIsVisible('billing_name') ||
            $this->colIsVisible('billing_firstname') ||
            $this->colIsVisible('billing_lastname') ||
            $this->colIsVisible('billing_middlename') ||
            $this->colIsVisible('billing_company') ||
            $this->colIsVisible('billing_street') ||
            $this->colIsVisible('billing_city') ||
            $this->colIsVisible('billing_region') ||
            $this->colIsVisible('billing_postcode') ||
            $this->colIsVisible('billing_email') ||
            $this->colIsVisible('billing_telephone') ||
            $this->colIsVisible('billing_country') ||
            $this->colIsVisible('billing_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_billing' => Mage::getConfig()->getTablePrefix().'sales_flat_order_address'),
                'main_table.entity_id = soa_billing.parent_id AND soa_billing.address_type="billing"',
                array(
//                    'billing_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname)'),
                    'billing_full_form'     => new Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname, soa_billing.middlename, soa_billing.company, soa_billing.street, soa_billing.city, soa_billing.region, soa_billing.postcode, soa_billing.email, soa_billing.telephone, soa_billing.fax)'),
                    'billing_firstname'     => 'soa_billing.firstname',
                    'billing_lastname'      => 'soa_billing.lastname',
                    'billing_middlename'    => 'soa_billing.middlename',
                    'billing_company'       => 'soa_billing.company',
                    'billing_street'        => 'soa_billing.street',
                    'billing_city'          => 'soa_billing.city',
                    'billing_region'        => 'soa_billing.region',
                    'billing_postcode'      => 'soa_billing.postcode',
                    'billing_email'         => 'soa_billing.email',
                    'billing_telephone'     => 'soa_billing.telephone',
                    'billing_country'       => 'soa_billing.country_id',
                    'billing_fax'           => 'soa_billing.fax',
                )
            );
        }
        //
        if(Mage::getStoreConfig('ordermanage/products/includeproducts') || $this->colIsVisible('name') || $this->colIsVisible('sku'))
        {
            $collection->join(
                'sales/order_item',
                '`sales/order_item`.order_id=main_table.entity_id',
                array(
                    'name' => new Zend_Db_Expr('group_concat(distinct `sales/order_item`.name SEPARATOR ", ")'),
                    'sku' => new Zend_Db_Expr('group_concat(distinct `sales/order_item`.sku SEPARATOR ", ")'),
    //                    'qty_ordered'=>'qty_ordered',
                ),
                'GROUP BY `sales/order_item`.sku',
//                null,
                'left'
            );

            //$collection->groupByAttribute('entity_id');
            $collection->getSelect()->group('main_table.entity_id');
//            $collection->getSelect()->group('sales_flat_order_item.sku');

        }


        if(Mage::getStoreConfig('ordermanage/columns/hide_status'))
        {
            $excludeStatuses = explode(',', Mage::getStoreConfig('ordermanage/columns/hide_status'));
            $collection->addAttributeToFilter('main_table.status', array('nin' => $excludeStatuses));
        }

/*
        ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_street', 'order_address/street', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_company', 'order_address/company', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_city', 'order_address/city', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_region', 'order_address/region', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_country', 'order_address/country_id', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_postcode', 'order_address/postcode', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_telephone', 'order_address/telephone', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_fax', 'order_address/fax', 'billing_address_id', null, 'left')
        ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_street', 'order_address/street', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_company', 'order_address/company', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_city', 'order_address/city', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_region', 'order_address/region', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_country', 'order_address/country_id', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_telephone', 'order_address/telephone', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_fax', 'order_address/fax', 'shipping_address_id', null, 'left')
        */

//        $collection = Mage::getResourceModel('sales/order')->getCollection();
//        $collection->joinTable('sales_flat_order', 'order_flat_order_grid.entity_id = order_flat_order.entity_id');
//        $collection = Mage::getResourceModel('sales/order_collection')
//        ->addAttributeToSelect('*');
//        $collection->addAttributeToSelect('*');
//        $collection->removeAttributeToSelect('store_name');
        /*
        $collection->join(
            'sales/order_item',
            'order_id=entity_id',
            array('name'=>'name', 'sku' =>'sku', 'qty_ordered'=>'qty_ordered'),
            null,
            'left'
        );
        */
//$collection->printLogQuery(true);
        return $collection;
    }
/*SELECT `main_table`.*, `sog`.`shipping_name`, `sog`.`billing_name`, CONCAT_WS(' ', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax) AS `shipping_full_form`, `soa_shippment`.`firstname` AS `shipping_firstname`, `soa_shippment`.`lastname` AS `shipping_lastname`, `soa_shippment`.`middlename` AS `shipping_middlename`, `soa_shippment`.`company` AS `shipping_company`, `soa_shippment`.`street` AS `shipping_street`, `soa_shippment`.`city` AS `shipping_city`, `soa_shippment`.`region` AS `shipping_region`, `soa_shippment`.`postcode` AS `shipping_postcode`, `soa_shippment`.`email` AS `shipping_email`, `soa_shippment`.`telephone` AS `shipping_telephone`, `soa_shippment`.`country_id` AS `shipping_country`, `soa_shippment`.`fax` AS `shipping_fax`, group_concat(`sales/order_item`.name SEPARATOR ", ") AS `name`, group_concat(`sales/order_item`.sku SEPARATOR ", ") AS `sku` `
    . `
    FROM `sales_flat_order` AS `main_table`
    LEFT JOIN `sales_flat_order_grid` AS `sog` ON main_table.entity_id = sog.entity_id
    LEFT JOIN `sales_flat_order_address` AS `soa_shippment` ON main_table.entity_id = soa_shippment.parent_id AND soa_shippment.address_type="shipping"
    INNER JOIN `sales_flat_order_item` AS `sales/order_item` ON `sales/order_item`.order_id=main_table.entity_id

GROUP BY `main_table`.`entity_id`*/
    protected function _prepareCollection_OrderedItems()
    {
//        return 'sales/order_collection';

        $collection = Mage::getResourceModel('sales/order_item_collection')->addAttributeToSelect('*');
/*
 */
        $collection->getSelect()->columns(
                array(
                    new Zend_Db_Expr('SUM(qty_ordered) as qty_total'),
                    new Zend_Db_Expr('GROUP_CONCAT(qty_ordered SEPARATOR ",") as qty'),
//                    new Zend_Db_Expr('REPLACE(GROUP_CONCAT(product_options SEPARATOR "}*{ "), \'"\', \'\\"\') as product_options'),
                    new Zend_Db_Expr('GROUP_CONCAT(item_id SEPARATOR ",") as ordered_item_ids'),
                )
            );
//        $collection->addExpressionAttributeToSelect('qty_total', 'SUM({{attribute}})', 'qty_ordered');

        if(Mage::getStoreConfig('ordermanage/orderedItemsMode/group'))
        {
            $collection->getSelect()->group('main_table.sku');
        }else
        {
            $collection->getSelect()->group('main_table.item_id');
        }

        $filter = $this->getParam($this->getVarNameFilter());
        if($filter)
        {
            $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
            if(isset($filter_data['status']))
            {
                $qtyStatus = trim($filter_data['status']);

                if($qtyStatus != '')
                {

                    //$actuallyOrdered = $ordered - $canceled - $refunded;
                    //qty_backordered
                    //qty_canceled
                    //qty_invoiced
                    //qty_ordered
                    //qty_refunded
                    //qty_shipped
                    $actuallyOrdered = '(qty_ordered - qty_canceled - qty_refunded)';
                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_PENDING) {
                        //!$invoiced && !$shipped && !$refunded && !$canceled && !$backordered
                        $collection->addAttributeToFilter('qty_invoiced', array('eq' => 0));
                        $collection->addAttributeToFilter('qty_shipped', array('eq' => 0));
                        $collection->addAttributeToFilter('qty_refunded', array('eq' => 0));
                        $collection->addAttributeToFilter('qty_canceled', array('eq' => 0));
                        $collection->addAttributeToFilter('qty_backordered', array('null' => true));

                    }
                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_SHIPPED) {
                     //$actuallyOrdered = $ordered - $canceled - $refunded;
                       // $shipped && $invoiced && ($actuallyOrdered == $shipped
                        $collection->addAttributeToFilter('qty_shipped', array('gt' => 0));
                        $collection->addAttributeToFilter('qty_invoiced', array('gt' => 0));
                        $collection->getSelect()->where($actuallyOrdered.' = qty_shipped');
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_INVOICED) {
                     //$actuallyOrdered = $ordered - $canceled - $refunded;
                       //$invoiced && !$shipped && ($actuallyOrdered == $invoiced)
                        $collection->addAttributeToFilter('qty_shipped', array('eq' => 0));
                        $collection->addAttributeToFilter('qty_invoiced', array('gt' => 0));
                        $collection->getSelect()->where($actuallyOrdered.' = qty_invoiced');
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_BACKORDERED) {
                     //$actuallyOrdered = $ordered - $canceled - $refunded;
                       //$backordered && ($actuallyOrdered == $backordered
                        $collection->addAttributeToFilter('qty_backordered', array('gt' => 0));
                        $collection->getSelect()->where($actuallyOrdered.' = qty_backordered');
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_REFUNDED) {
                        //$refunded && $ordered == $refunded
                        $collection->addAttributeToFilter('qty_refunded', array('gt' => 0));
                        $collection->getSelect()->where('qty_ordered = qty_refunded');
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_CANCELED) {
                        //$canceled && $ordered == $canceled
                        $collection->addAttributeToFilter('qty_canceled', array('gt' => 0));
                        $collection->getSelect()->where('qty_ordered = qty_canceled');
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_PARTIAL) {
                        //max($shipped, $invoiced) < $actuallyOrdered
                        $collection->getSelect()->where('GREATEST (qty_shipped, qty_invoiced) < '.$actuallyOrdered);
                    }

                    if ($qtyStatus == Mage_Sales_Model_Order_Item::STATUS_RETURNED) {
                        $collection->getSelect()->where('1 = 0');
                    }
                    //return self::STATUS_RETURNED; - NOT USED IN MAGENTO NOW
                    //return self::STATUS_MIXED;

//                    $collection->printLogQuery(true);
                }
            }
        }



/*
        $collection->getSelect()->joinLeft(
            array('sog' => 'sales_flat_order_grid'),
//            array('sog' => 'sales/order_grid_collection'),
            'main_table.entity_id = sog.entity_id',
            array(
                'sog.shipping_name',
                'sog.billing_name'
            )
        );
*/
//        $collection->getSelect()->joinLeft(array('sfo'=>'sales_flat_order'),'sfo.entity_id=main_table.entity_id',array('sfo.customer_email','sfo.weight','sfo.discount_description','sfo.increment_id','sfo.store_id','sfo.created_at','sfo.status','sfo.base_grand_total','sfo.grand_total')); // New


/*        if(Mage::getStoreConfig('ordermanage/products/includeproducts') || $this->colIsVisible('name') || $this->colIsVisible('sku'))
        {
            $collection->join(
                'sales/order_item',
                '`sales/order_item`.order_id=main_table.entity_id',
                array(
                    'name' => new Zend_Db_Expr('group_concat(`sales/order_item`.name SEPARATOR ", ")'),
                    'sku' => new Zend_Db_Expr('group_concat(`sales/order_item`.sku SEPARATOR ", ")'),
    //                    'qty_ordered'=>'qty_ordered',
                ),
                null,
                'left'
            );

            //$collection->groupByAttribute('entity_id');
//            $collection->getSelect()->group('main_table.entity_id');
        }
*/

/*        if(Mage::getStoreConfig('ordermanage/columns/hide_status'))
        {
            $excludeStatuses = explode(',', Mage::getStoreConfig('ordermanage/columns/hide_status'));
            $collection->addAttributeToFilter('main_table.status', array('nin' => $excludeStatuses));
        }
*/
        return $collection;
    }





    protected function _prepareCollection()
    {
        $collection = null;

        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_STANDARD)
        {
            $collection = $this->_prepareCollection_Orders();
        }else
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_ORDER_ITEMS)
        {
            $collection = $this->_prepareCollection_OrderedItems();
        }

        $this->setCollection($collection);
//        $collection->printLogQuery(true);echo '<br/><br/>';
//        die();

        return parent::_prepareCollection();
    }




    protected function _prepareColumns_OrdersGrid()
    {
        $resource       =   Mage::getSingleton('core/resource');
        $readConnection =   $resource->getConnection('core_read');
        $tableName      =   $resource->getTableName('sales/order_address');
        $columnsCollection = $readConnection->fetchAll("SHOW COLUMNS FROM ".$tableName);


        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $dispatch_user = Mage::getStoreConfig('customconfig_options/section_two/dispatch');
        $operation_user = Mage::getStoreConfig('customconfig_options/section_two/operation');
        $role_id = $role_data->getRoleId();


        $store = $this->_getStore();

        if($this->colIsVisible('increment_id'))
        {
            $this->addColumn('increment_id', array(
                'header'=> Mage::helper('sales')->__('Order #'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'increment_id',
                'filter_index' => 'main_table.increment_id',
            ));
        }
        if($this->colIsVisible('created_at'))
        {
            $this->addColumn('created_at', array(
                'header' => Mage::helper('sales')->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime',
                'width' => '100px',
                'filter_index' => 'main_table.created_at'
            ));
        }

        if (!Mage::app()->isSingleStoreMode())
        {
            if($this->colIsVisible('store_id'))
            {
                $this->addColumn('store_id', array(
                    // 'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                    'header'    => Mage::helper('sales')->__('Branch'),
                    'index'     => 'store_id',
                    'filter_index' => 'main_table.store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                ));
            }
        }

        if($this->colIsVisible('billing_name'))
        {
            $this->addColumn('billing_name', array(
                'header'        =>  Mage::helper('sales')->__('Bill to Name'),
                'index'         =>  'billing_name',
                'width' => '80px',
                'filter_index'  =>  'sog.billing_name',
            ));
        }
        /*Change on 12/9 by Dh*/
        $this->addColumn('update_status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'update_status',
            'type'  => 'options',
            'filter' => 'adminhtml/widget_grid_column_filter_select',
            'filter_index' => 'main_table.order_status',
            'width' => '70px',
            'renderer' => 'dirvermanagement/adminhtml_order_renderer_status',
            'options' => $this->_getUpdatedStatusIds(),
        ));

        if($this->colIsVisible('shipping_name'))
        {
            $this->addColumn('shipping_name', array(
                'header'        =>  Mage::helper('sales')->__('Ship to Name'),
                'index'         =>  'shipping_name',
                'filter_index'  =>  'sog.shipping_name',
            ));
        }

        if($this->colIsVisible('base_grand_total'))
        {
            $this->addColumn('base_grand_total', array(
                'header' => Mage::helper('sales')->__('G.T. (Base)'),
                'index' => 'base_grand_total',
                'filter_index' => 'main_table.base_grand_total',
                'type'  => 'currency',
                'currency' => 'base_currency_code',
            ));
        }

        if($this->colIsVisible('grand_total'))
        {
            $this->addColumn('grand_total', array(
                'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
                'index' => 'grand_total',
                'filter_index' => 'main_table.grand_total',
                'type'  => 'currency',
                'currency' => 'order_currency_code',
            ));
        }

        if($this->colIsVisible('status'))
        {
            $this->addColumn('status', array(
                'header' => Mage::helper('sales')->__('Status'),
                'index' => 'status',
                'filter_index' => 'main_table.status',
                //'type'  => 'options',
                'type'  => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                'renderer' => 'Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderStatus',
            ));
        }

        $ignoreCols = array(
            'increment_id'      =>  true,
            'store_id'          =>  true,
            'created_at'        =>  true,
            'billing_name'      =>  true,
            'shipping_name'     =>  true,
            'base_grand_total'  =>  true,
            'grand_total'       =>  true,
            'status'            =>  true,
        );

        $defaults = array(
            'remote_ip' => array(
                'header' => Mage::helper('sales')->__('Remote IP'),
                'index' => 'remote_ip',
                'type'  => 'ip',
//                'width' => '70px',
//                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ),

        );

        foreach(self::$columnSettings as $col => $true)
        {
            if(isset($ignoreCols[$col]))
                continue;

            if(isset($defaults[$col]))
            {
                $innerSettings = $defaults[$col];
            } else
            if(isset(self::$columnType[$col]))
            {

                $innerSettings = array(
                    'header'=> (isset(self::$columnType[$col]['title'])) ? Mage::helper('sales')->__(self::$columnType[$col]['title']) : Mage::helper('sales')->__($col),
                    'width' => '80px',
//                    'type'  => self::$columnType[$col]['type'],
                    'type'  => self::$columnType[$col]['type'] != 'country' ? self::$columnType[$col]['type'] : 'select',
                );

                if(isset(self::$columnType[$col]['filter_index']))
                {
                    $innerSettings['filter_index'] = self::$columnType[$col]['filter_index'];
                }


                if(self::$columnType[$col]['type'] == 'country')
                {
                    $innerSettings['filter'] =  'Iksanika_Ordermanage_Model_System_Config_Source_Columns_Country';


                    $countriesList = array();
                    $countries = Mage::getResourceModel('directory/country_collection')->loadByStore()->toOptionArray();
                    foreach($countries as $country)
                    {
                        $countriesList[$country['value']] = $country['label'];
                    }
                    $innerSettings['options'] = $countriesList;
                }
            } else
            {
                $innerSettings = array(
//                    'header'=> Mage::helper('catalog')->__($col),
                    'header'=> Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $col))),
                    'width' => '80px',
                    'type'  => 'text',

                    // enhacement for Franco comcast
//                    'type'  => 'input',
                );


                if($col == "shipping_tracking_number")
                {
                    $innerSettings['renderer'] = 'Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_Shippment';
                    $innerSettings['sortable'] = false;
                }

            }
            $innerSettings['index'] = $col;
            $innerSettings['filter_index'] = isset($innerSettings['filter_index']) ? $innerSettings['filter_index'] : 'main_table.'.$col;

            // @TODO: remove this part - start, add to prepareCollection conditions to include it from DB query, and remove call from grid.phtml
            if($col == 'payment_method' || $col == 'payment_code' || $col == 'payment_cc_type' || $col == 'special_qty_uniq_prods' ||
                $col == 'shipping_tracking_number')
            {
                $innerSettings['filter']    =   false;
                $innerSettings['sortable']  =   false;
            }
            // @TODO: remove this part - end
            //echo $col;

            if($col != "shipping_region")
            {
            $this->addColumn($col, $innerSettings);
            }
        }
        // $this->addColumn('shipping_arrival_date', array(
        //     'header' => Mage::helper('sales')->__('Delivery Date'),
        //     'index' => 'shipping_arrival_date',
        //     'type' => 'datetime',
        //     'width' => '100px',
        // ));

        $this->addColumn('webpos_staff_id', array(
            'header' => Mage::helper('sales')->__('Pos User'),
            'index' => 'webpos_staff_id',
            'type'  => 'options',
            'filter_index' => 'main_table.webpos_staff_id',
            'options' => $this->_getWebposStaffIds()
        ));

        $this->addColumn('shipping_region', array(
                'header'        =>  Mage::helper('sales')->__('Ship to Area'),
                'index'         =>  'shipping_region',
                'filter'    => false,
                'sortable'  => false,
        ));

        if($role_data->getRoleId() == $dispatch_user || $role_data->getRoleId() == $operation_user || $role_data->getRoleId() == 1){
            $this->addColumn('driver_id', array(
                'header' => Mage::helper('sales')->__('Delivery'),
                'index' => 'driver_id',
                'type'  => 'text',
                'filter'    => false,
                'sortable'  => false,
                'width' => '80px',
                'renderer'         => 'ordermanage/sales_order_renderer_driver',
            ));
        }

       //  $this->addColumn('entity_id', array(
       //      'header' => Mage::helper('sales')->__('Delivery Method'),
       //      'width' => '80px',
       //      'index'  => 'entity_id',
       //      'type'  => 'textarea',
       //      'filter' => false,
       //      'renderer' => 'ordermanage/sales_order_renderer_method',
       // ));

        $this->addColumn('shipping_description', array(
                'header'        =>  Mage::helper('sales')->__('Delivery Method'),
                'index'         =>  'shipping_description',
                'filter'    => false,
                'sortable'  => false,
        ));

        $this->addColumn('shipping_delivery_date', array(
            'header' => Mage::helper('sales')->__('Delivery Date'),
            'index' => 'shipping_delivery_date',
            'type'  => 'datetime',
            'filter_index' => 'main_table.shipping_delivery_date',
            'sortable'  => true,
            //'renderer' => 'ordermanage/sales_order_renderer_date',
        ));
        /*$this->addColumn('order_status', array(
            'header' => Mage::helper('sales')->__('Order Status'),
            'index' => 'order_status',
            'type'  => 'options',
            'options' => $this->_getOrderStatusAttributeOptions($role_id),
            'renderer'         => 'dirvermanagement/adminhtml_order_renderer_orderstatus',
        ));*/

        /*$this->addColumn('is_survey', array(
            'header' => Mage::helper('sales')->__('Is Survey'),
            'index' => 'is_survey',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false,
            'width' => '250px',
            'renderer' => 'dirvermanagement/adminhtml_order_renderer_survey',
        ));
        */

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view'))
        {
            $this->addColumn('action',
                array(
                    'header'    =>  Mage::helper('sales')->__('Action'),
                    'width'     =>  '50px',
                    'type'      =>  'action',
                    'getter'    =>  'getId',
                    'actions'   =>  array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    =>  false,
                    'sortable'  =>  false,
                    'index'     =>  'stores',
                    'is_system' =>  true,
            ));
        }
    }






    protected function _prepareColumns_OrderedItemsGrid()
    {
        $store = $this->_getStore();
/*
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'item_id',
        ));
*/

        $this->addColumn('date',
            array(
                'header'=> Mage::helper('catalog')->__('Created Date'),
                'name' => 'created_at',
                'index' => 'created_at',
                'type' => 'date',
        ));

        $imgWidth = Mage::getStoreConfig('ordermanage/images/width')."px";

        $this->addColumn('small_image',
            array(
                'header'=> Mage::helper('catalog')->__('Small Img'),
                'type'  => 'image',
                'width' => $imgWidth,
                'index' => 'small_image',
                'filter' => false,
                'sortable' => false,
                'renderer' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_ImageOrderedItem,
        ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'name' => 'pu_name[]',
                'index' => 'name'/*,
                'width' => '150px'*/
        ));

        $this->addColumn('qty_total',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '80px',
                'index' => 'qty_total',
                'name' => 'qty_total',
                'type' => 'text'
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
                'name' => 'sku',
                'type' => 'text'
        ));

        $this->addColumn('order_id',
            array(
                'header'=> Mage::helper('catalog')->__('Orders Id'),
                'width' => '80px',
                'index' => 'order_id',
                'name' => 'order_id',
                'type' => 'text',
                'sortable' => false,
                'filter' => false,
                'renderer' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderId,
        ));

        $this->addColumn('product_options',
            array(
                'header'=> Mage::helper('catalog')->__('Options'),
                'width' => '80px',
//                'index' => 'product_options',
//                'name' => 'product_options',
                'index' => 'ordered_item_ids',
                'name' => 'ordered_item_ids',
                'type' => 'text',
                'sortable' => false,
                'filter' => false,
                'renderer' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderedItemOptions,
        ));

        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty Items'),
                'width' => '80px',
                'index' => 'qty_ordered',
                'name' => 'qty_ordered',
                'sortable' => false,
                'filter' => false,
                'renderer' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderedItemQty,
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '80px',
                'index' => 'status',
                'name' => 'status',
                'filter' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Filter_OrderItemStatus,
                'renderer' => Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_OrderItemStatus,
//                'type' => 'select',
                'type'  => 'options',
                'options' => Mage_Sales_Model_Order_Item::getStatuses(),
        ));
//        var_dump(Iksanika_Ordermanage_Model_System_Config_Source_Columns_StatusesOrderItem::toOptionArray());
/*
        if($this->colIsVisible('qty'))
        {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'input',
                    'index' => 'qty',
                    'name' => 'pu_qty[]',
                    'renderer' => 'Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_Number',
            ));
        }
*/

/*
        if($this->colIsVisible('status'))
        {
            $this->addColumn('status',
                array(
                    'header'=> Mage::helper('catalog')->__('Status'),
                    'width' => '70px',
                    'index' => 'status',
//                    'type'  => 'iks_options',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
                    'renderer' => 'Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_Options',
            ));
        }
*/
/*
        $this->addColumn('view', array(
            'header' => Mage::helper('catalog')->__('View'),
            'width' => '40px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('catalog')->__('View'),
                    'url' => array(
                        'base' => 'catalog/product/view',
                        'params' => array('store' => $this->getRequest()->getParam('store'))
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));
*/

//        $this->setDestElementId('edit_form');

    }



    protected function _prepareColumns()
    {
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_STANDARD)
        {
            $this->_prepareColumns_OrdersGrid();
        }else
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_ORDER_ITEMS)
        {
            $this->_prepareColumns_OrderedItemsGrid();
        }

        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('ordermanage/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('ordermanage/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_STANDARD)
        {
            $this->setMassactionIdField('entity_id');
            $this->getMassactionBlock()->setFormFieldName('order_ids');
            $this->getMassactionBlock()->setUseSelectAll(false);

            // if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            //     $this->getMassactionBlock()->addItem('cancel_order', array(
            //          'label'=> Mage::helper('sales')->__('Cancel'),
            //          'url'  => $this->getUrl('*/sales_order/massCancel'),
            //     ));
            // }

            // if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            //     $this->getMassactionBlock()->addItem('hold_order', array(
            //          'label'=> Mage::helper('sales')->__('Hold'),
            //          'url'  => $this->getUrl('*/sales_order/massHold'),
            //     ));
            // }

            // if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            //     $this->getMassactionBlock()->addItem('unhold_order', array(
            //          'label'=> Mage::helper('sales')->__('Unhold'),
            //          'url'  => $this->getUrl('*/sales_order/massUnhold'),
            //     ));
            // }

            $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
                 'label'=> Mage::helper('sales')->__('Print Invoices'),
                 'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
            ));

            $this->getMassactionBlock()->addItem('pdfshipments_order', array(
                 'label'=> Mage::helper('sales')->__('Print Packingslips'),
                 'url'  => $this->getUrl('*/sales_order/pdfshipments'),
            ));

            $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
                 'label'=> Mage::helper('sales')->__('Print Credit Memos'),
                 'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
            ));

            $this->getMassactionBlock()->addItem('pdfdocs_order', array(
                 'label'=> Mage::helper('sales')->__('Print All'),
                 'url'  => $this->getUrl('*/sales_order/pdfdocs'),
            ));

            $this->getMassactionBlock()->addItem('print_shipping_label', array(
                 'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
                 'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
            ));

            /*
             * Prepare list of columns for update
             */
            // $this->getMassactionBlock()->addItem('otherDivider', $this->getSubDivider("------Additional------"));
            // $fields = self::getColumnForUpdate();

            // $this->getMassactionBlock()->addItem('save',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Update'),
            //         'url'   => $this->getUrl('*/*/massUpdateOrders', array('_current'=>true)),
            //         'fields' => $fields
            //     )
            // );

            // // Send Email
            // $fields4Update = self::getColumnSettings();
            // $isShippingTrackingNumber = false;

            // foreach($fields4Update as $attributeCode => $attributeStatus)
            // {
            //     if($attributeCode == 'shipping_tracking_number')
            //     {
            //         $isShippingTrackingNumber = true;
            //     }
            // }

            // $fields4Update = array('order_ids');
            // if($isShippingTrackingNumber)
            // {
            //     $fields4Update[] = 'shipping_tracking_number';
            //     $fields4Update[] = 'shipping_tracking_number_carrier';
            // }


            // // Invoice
            // $this->getMassactionBlock()->addItem('massInvoice',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Invoice'),
            //         'url'   => $this->getUrl('*/*/massInvoiceCapture', array('_current'=>true)),
            //         'fields' => array('order_ids')
            //     )
            // );

            // // Invoice->Capture
            // $this->getMassactionBlock()->addItem('massInvoiceCapture',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Invoice').'->'.Mage::helper('catalog')->__('Capture'),
            //         'url'   => $this->getUrl('*/*/massInvoiceCapture', array('_current'=>true, 'proceedCapture' => true)),
            //         'fields' => array('order_ids')
            //     )
            // );

            // // Invoice->Capture->Ship
            // $this->getMassactionBlock()->addItem('massInvoiceCaptureShip',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Invoice').'->'.Mage::helper('catalog')->__('Capture').'->'.Mage::helper('catalog')->__('Ship'),
            //         'url'   => $this->getUrl('*/*/massInvoiceCapture', array('_current'=>true, 'proceedCapture' => true, 'proceedShipment' => true)),
            //         'fields' => $fields4Update,
            //     )
            // );






            // // Invoice->Capture
            // $this->getMassactionBlock()->addItem('massCapture',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Capture'),
            //         'url'   => $this->getUrl('*/*/massInvoiceCapture', array('_current'=>true, 'proceedCapture' => true)),
            //         'fields' => array('order_ids')
            //     )
            // );


            // // [Credit Memo]

            // // Ship
            // $this->getMassactionBlock()->addItem('saveShip',
            //     array(
            //         'label' => Mage::helper('catalog')->__('Ship'),
            //         'url'   => $this->getUrl('*/*/massShip', array('_current'=>true)),
            //         'fields' => $fields4Update,
            //     )
            // );

            // Capture
        }else
        if(Mage::getStoreConfig('ordermanage/columns/mode') == Iksanika_Ordermanage_Model_System_Config_Source_Columns_Mode::MODE_ORDER_ITEMS)
        {

        }

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view'))
        {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId() , 'advanced'=>true));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function getSubDivider($divider="-------") {
        $dividerTemplate = array(
          'label' => '--------'.$this->__($divider).'--------',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }






    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";


        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem())
                {
                    $colIndex = $column->getIndex();
                    $colContent = $item->$colIndex;
                    if($colIndex == 'category_ids')
                        $colContent = implode(',', $item->getCategoryIds());
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $colContent).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }
    public function _getOrderStatusAttributeOptions($role_id)
    {
        $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');

        if($userrole_status->getAllowedStatus()){
            $role_status = explode(',', $userrole_status->getViewdStatus());
        }

        $options = array();
        $orderStatus = Mage::helper('customconfig')->getCustomStatus();
        $customstatus =array();
        foreach($orderStatus as $key => $status) {
            $customstatus[$status['value']] = $status['label'];

        }
        foreach ($role_status as $status) {
            $options[$status] = $customstatus[$status];
        }
        return $options;
    }

    public function _getWebposStaffIds()
    {
        $webshopusers = Mage::getModel('webpos/user')->getCollection()->getData();
        $options = array();
        foreach ($webshopusers as $webshopusers_data) {
            $options[$webshopusers_data['user_id']] = $webshopusers_data['username'];
        }
        return $options;
    }

    public function _getUpdatedStatusIds()
    {
      $admin_user_session = Mage::getSingleton('admin/session');
      $adminuserId = $admin_user_session->getUser()->getUserId();
      $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
      $role_id = $role_data->getRoleId();
      $driver_userId =  Mage::getStoreConfig('customconfig_options/section_two/driver');
      $dispatch_userId =  Mage::getStoreConfig('customconfig_options/section_two/dispatch');
      $operation_userId =  Mage::getStoreConfig('customconfig_options/section_two/operation');


      $userrole_status = Mage::getModel('customconfig/customconfig')->load($role_id,'role_id');
      if($userrole_status->getAllowedStatus()){
          $role_status = explode(',', $userrole_status->getAllowedStatus());
      }
      $orderStatus = Mage::helper('customconfig')->getCustomStatus();
      $customstatus =array();
      foreach($orderStatus as $key => $status) {
          $customstatus[$status['value']] = $status['label'];

      }

      foreach ($role_status as $status) {
          if($role_id != 1){
              $data_array[$status] = $customstatus[$status];
          }
      }
      if($role_id == 1){
          $data_array = array();
          $data_array = $customstatus;
      }

      //  echo "<pre>";print_R($data_array);die;

      return $data_array;
    }

    public function getKitcherUsercatIds(){
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $kitchen = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        $role_id = $role_data->getRoleId();
        if($role_id != 1 && $role_id == $kitchen){
            $user_category = Mage::getModel('customconfig/usercategory')->load($adminuserId,'user_id');
            $user_cat = explode(',', $user_category->getCatIds());
            // $collection->addFieldToFilter('category_id', array(array('finset'=> array($user_cat))));
            return $user_cat;
        }
        return;
    }

}
