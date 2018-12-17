<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Products Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    const SELECT_COUNT_SQL_TYPE_CART           = 1;

    /**
     * Product entity identifier
     *
     * @var int
     */
    protected $_productEntityId;

    /**
     * Product entity table name
     *
     * @var string
     */
    protected $_productEntityTableName;

    /**
     * Product entity type identifier
     *
     * @var int
     */
    protected $_productEntityTypeId;

    /**
     * select count
     *
     * @var int
     */
    protected $_selectCountSqlType               = 0;

    /**
     * Init main class options
     *
     */
    public function __construct()
    {
        $product = Mage::getResourceSingleton('catalog/product');
        /* @var $product Mage_Catalog_Model_Entity_Product */
        $this->setProductEntityId($product->getEntityIdField());
        $this->setProductEntityTableName($product->getEntityTable());
        $this->setProductEntityTypeId($product->getTypeId());

        parent::__construct();
    }
    /**
     * Set Type for COUNT SQL Select
     *
     * @param int $type
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setSelectCountSqlType($type)
    {
        $this->_selectCountSqlType = $type;
        return $this;
    }

    /**
     * Set product entity id
     *
     * @param int $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityId($entityId)
    {
        $this->_productEntityId = (int)$entityId;
        return $this;
    }

    /**
     * Get product entity id
     *
     * @return int
     */
    public function getProductEntityId()
    {
        return $this->_productEntityId;
    }

    /**
     * Set product entity table name
     *
     * @param string $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityTableName($value)
    {
        $this->_productEntityTableName = $value;
        return $this;
    }

    /**
     * Get product entity table name
     *
     * @return string
     */
    public function getProductEntityTableName()
    {
        return $this->_productEntityTableName;
    }

    /**
     * Set product entity type id
     *
     * @param int $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityTypeId($value)
    {
        $this->_productEntityTypeId = $value;
        return $this;
    }

    /**
     * Get product entity tyoe id
     *
     * @return int
     */
    public function getProductEntityTypeId()
    {
        return  $this->_productEntityTypeId;
    }

    /**
     * Join fields
     *
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    protected function _joinFields()
    {
        $this->_totals = new Varien_Object();

        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price');

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return unknown
     */
    public function getSelectCountSql()
    {
        if ($this->_selectCountSqlType == self::SELECT_COUNT_SQL_TYPE_CART) {
            $countSelect = clone $this->getSelect();
            $countSelect->reset()
                ->from(
                    array('quote_item_table' => $this->getTable('sales/quote_item')),
                    array('COUNT(DISTINCT quote_item_table.product_id)'))
                ->join(
                    array('quote_table' => $this->getTable('sales/quote')),
                    'quote_table.entity_id = quote_item_table.quote_id AND quote_table.is_active = 1',
                    array()
                );
            return $countSelect;
        }

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");

        return $countSelect;
    }

    /**
     * Add carts count
     *
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addCartsCount()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset();

        $countSelect->from(array('quote_items' => $this->getTable('sales/quote_item')), 'COUNT(*)')
            ->join(array('quotes' => $this->getTable('sales/quote')),
                'quotes.entity_id = quote_items.quote_id AND quotes.is_active = 1',
                array())
            ->where("quote_items.product_id = e.entity_id");

        $this->getSelect()
            ->columns(array("carts" => "({$countSelect})"))
            ->group("e.{$this->getProductEntityId()}")
            ->having('carts > ?', 0);

        return $this;
    }

    /**
     * Add orders count
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrdersCount($from = '', $to = '')
    {
        $orderItemTableName = $this->getTable('sales/order_item');
        $productFieldName   = sprintf('e.%s', $this->getProductEntityId());

        $this->getSelect()
            ->joinLeft(
                array('order_items' => $orderItemTableName),
                "order_items.product_id = {$productFieldName}",
                array())
            ->columns(array('orders' => 'COUNT(order_items2.item_id)'))
            ->group($productFieldName);

        $dateFilter = array('order_items2.item_id = order_items.item_id');
        if ($from != '' && $to != '') {
            $dateFilter[] = $this->_prepareBetweenSql('order_items2.created_at', $from, $to);
        }

        $this->getSelect()
            ->joinLeft(
                array('order_items2' => $orderItemTableName),
                implode(' AND ', $dateFilter),
                array()
            );

        return $this;
    }

    /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrderedQty($from = '', $to = '')
    {
        /* Ewall Custom Code*/
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole();
        $kitchen = Mage::getStoreConfig('customconfig_options/section_two/kitchen');
        $role_id = $role_data->getRoleId();
        // if($role_id == $kitchen){
        //     $user_category = Mage::getModel('customconfig/usercategory')->load($adminuserId,'user_id');
        //     $user_cat = explode(',', $user_category->getCatIds());
        //     $this->addKitchenOrderedQty($from , $to, $user_cat);
        //     return $this;
        // }elseif(Mage::app()->getRequest()->getControllerName() == 'adminhtml_kitchenuser'){
        //     $this->addKitchenOrderedQty($from , $to, 2);
        //     return $this;
        // }

        if(Mage::app()->getRequest()->getControllerName() == 'adminhtml_kitchenuser'){
            if($role_id == $kitchen){
                $user_category = Mage::getModel('customconfig/usercategory')->load($adminuserId,'user_id');
                $user_cat = explode(',', $user_category->getCatIds());
                $this->addKitchenOrderedQty($from , $to, $user_cat);
               
                return $this;
            }else{
                $this->addKitchenOrderedQty($from , $to, 2);
                Mage::log(3 ,null,'mylog55.log');
                return $this;
            }            
        }

        if(Mage::app()->getRequest()->getControllerName() == 'adminhtml_customoptionreport'){
            $this->addCustomOptionTextOrderedQty($from , $to);
            return $this;
        }
		
		if(Mage::app()->getRequest()->getControllerName() == 'adminhtml_orderreport'){
            $this->addOrderedReportQty($from , $to);
            return $this;
        }

        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        
        $this->getSelect()->reset()
            ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            ->joinLeft(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
        return $this;
    }

    

    public function addKitchenOrderedQty($from,$to,$user_cat){


        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {

            // $fieldName            = $orderTableAliasName . '.shipping_arrival_date';
            // $orderdeliveryJoinCondition = array($this->_prepareBetweenSql($fieldName, $from, $to));
            // $fieldName            = $orderTableAliasName . '.created_at';
            // $orderdeliveryJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
            //$newCondition = implode(' OR ', $orderdeliveryJoinCondition);
            $deliveryfieldName            = $orderTableAliasName . '.shipping_delivery_date';
            $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
            $createfieldName            = $orderTableAliasName . '.created_at';
            $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
            $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
            array_push($orderJoinCondition,'('.$newCondition.')');
        }

        
// edit for order manager by islam Elgarhy 
        $regxCat= ",(";
        foreach($user_cat as $uc)
        {
            $regxCat = $regxCat. $uc .'|';
        }
        
        $regxCat = substr($regxCat, 0, -1);
        $regxCat = $regxCat . "),";
        

// edit for order manager by islam Elgarhy 

        $this->getSelect()->reset()
            ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name',
                    'custom_options' => 'order_items.custom_options',
                    'product_id' => 'order_items.product_id',
                    'sku' => 'order_items.sku'

                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            // ->joinLeft(
            //     array('e' => $this->getProductEntityTableName()),
            //     implode(' AND ', $productJoinCondition),
            //     array(
            //         'entity_id' => 'order_items.product_id',
            //         'entity_type_id' => 'e.entity_type_id',
            //         'attribute_set_id' => 'e.attribute_set_id',
            //         'type_id' => 'e.type_id',
            //         'sku' => 'e.sku',
            //         'has_options' => 'e.has_options',
            //         'required_options' => 'e.required_options',
            //         'created_at' => 'e.created_at',
            //         'updated_at' => 'e.updated_at'
            //     ))
            ->where('parent_item_id IS NULL')
            ->where('order_items.item_status != ?', 'ready')
            ->orWhere('order_items.item_status IS NULL')
            //->where('order_items.category_id IN (?)', $user_cat)
            ->where('order_items.category_id  REGEXP ?', $regxCat)
            ->group(array('order_items.product_id', 'order_items.custom_options')) 
            ->having('SUM(order_items.qty_ordered) > ?', 0);

            
        return $this;
    }

    public function addCustomOptionTextOrderedQty($from,$to)
	{
		//echo "<pre>"; print_r(Mage::registry('delivery-filters')); exit;
        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $deliveryfieldName            = $orderTableAliasName . '.shipping_delivery_date';
            $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
            $createfieldName            = $orderTableAliasName . '.created_at';
            $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
            $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
            array_push($orderJoinCondition,'('.$newCondition.')');
			
			
        }

		$url=$_SERVER['REQUEST_URI'];
		$more_info = explode('/', $url);
		if (in_array("withoutmachine", $more_info))
		{	
			
			$condition="text_custom_options_value not like 'Machine%'";	
		}		
		else
		{
			//->where("is_text_custom_option like 'Machine%'")
			$condition="text_custom_options_value like 'Machine%'";	
		}
		
	 
		
		//echo "<pre>"; print_r($more_info); exit;
		
        $this->getSelect()->reset()
            ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'order_id' => 'order_items.order_id',
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name',
                    'custom_options' => 'order_items.custom_options',
                    'is_text_custom_option' => 'order_items.is_text_custom_option',
                    'text_custom_options_value' => 'order_items.text_custom_options_value',
                    'product_id' => 'order_items.product_id',
                    'sku' => 'order_items.sku'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array(
                    'increment_id' => 'order.increment_id',
                    'created_at' => 'order.created_at',
                    'shipping_delivery_date' =>  'order.shipping_delivery_date',
                    'store_id' => 'order.store_id',
                    'store_name' => 'order.store_name'
                    ))
			
            ->where('parent_item_id IS NULL')
            ->where('is_text_custom_option IS NOT NULL')			
			->where($condition)	 
            ->group(array('order_items.item_id'))
            ->having('SUM(order_items.qty_ordered) > ?', 0);
		
		// echo $this->getSelect(); exit;
		
        return $this;
    }
    
	
		
	 public function addOrderedReportQty($from,$to)
	 {
		//$this->getRequest()->getRouteName();
		//echo "<pre>"; print_r(Mage::registry('delivery-filters')); exit;
        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $deliveryfieldName            = $orderTableAliasName . '.shipping_delivery_date';
            $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
            $createfieldName            = $orderTableAliasName . '.created_at';
            $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
            $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
            array_push($orderJoinCondition,'('.$newCondition.')');
			
			
        }

	 
		
		//echo "<pre>"; print_r($more_info); exit;
		
        $this->getSelect()->reset()
            ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'order_id' => 'order_items.order_id',
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name',
                    'custom_options' => 'order_items.custom_options',
                    'is_text_custom_option' => 'order_items.is_text_custom_option',
                    'text_custom_options_value' => 'order_items.text_custom_options_value',
                    'product_id' => 'order_items.product_id',
                    'sku' => 'order_items.sku'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array(
                    'increment_id' => 'order.increment_id',
                    'created_at' => 'order.created_at',
                    'shipping_delivery_date' =>  'order.shipping_delivery_date',
                    'store_id' => 'order.store_id',
                    'store_name' => 'order.store_name'
                    ))
			
            ->where('parent_item_id IS NULL')
            ->where('is_text_custom_option IS NOT NULL')			
            ->group(array('order_items.item_id'))
            ->having('SUM(order_items.qty_ordered) > ?', 0);
		
		// echo $this->getSelect(); exit;
		
        return $this;
    }	
		

    /**
     * Set order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, array('carts', 'orders', 'ordered_qty'))) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    /**
     * Add views count
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addViewsCount($from = '', $to = '')
    {
        /**
         * Getting event type id for catalog_product_view event
         */
        foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $this->getSelect()->reset()
            ->from(
                array('report_table_views' => $this->getTable('reports/event')),
                array('views' => 'COUNT(report_table_views.event_id)'))
            ->join(array('e' => $this->getProductEntityTableName()),
                $this->getConnection()->quoteInto(
                    "e.entity_id = report_table_views.object_id AND e.entity_type_id = ?",
                    $this->getProductEntityTypeId()))
            ->where('report_table_views.event_type_id = ?', $productViewEvent)
            ->group('e.entity_id')
            ->order('views ' . self::SORT_ORDER_DESC)
            ->having('COUNT(report_table_views.event_id) > ?', 0);

        if ($from != '' && $to != '') {
            $this->getSelect()
                ->where('logged_at >= ?', $from)
                ->where('logged_at <= ?', $to);
        }

        $this->_useAnalyticFunction = true;
        return $this;
    }

    /**
     * Prepare between sql
     *
     * @param  string $fieldName Field name with table suffix ('created_at' or 'main_table.created_at')
     * @param  string $from
     * @param  string $to
     * @return string Formatted sql string
     */
    protected function _prepareBetweenSql($fieldName, $from, $to)
    {
        return sprintf('(%s BETWEEN %s AND %s)',
            $fieldName,
            $this->getConnection()->quote($from),
            $this->getConnection()->quote($to)
        );
    }

    /**
     * Add store restrictions to product collection
     *
     * @param  array $storeIds
     * @param  array $websiteIds
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addStoreRestrictions($storeIds, $websiteIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }

        $filters = $this->_productLimitationFilters;
        if (isset($filters['store_id'])) {
            if (!in_array($filters['store_id'], $storeIds)) {
                $this->addStoreFilter($filters['store_id']);
            } else {
                $this->addStoreFilter($this->getStoreId());
            }
        } else {
            $this->addWebsiteFilter($websiteIds);
        }

        return $this;
    }
}
