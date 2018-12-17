<?php
class Ewall_CustomConfig_Block_Adminhtml_Orderstatussummary_Grid extends Mage_Adminhtml_Block_Report_Grid 
{

    protected $_exportVisibility = false;

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridOrderstatussummary');
        $this->setTemplate('ewall/customconfig/status_summary.phtml');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('reports/product_sold_collection');
        return $this;
    }
    protected function _prepareColumns()
    {
        return parent::_prepareColumns();
    }

    protected function _prepareStatussummaryCollections()
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $userdata = Mage::getModel('customconfig/usercategory')->load($adminuserId, 'user_id');
        if(($userdata->getAllowStoreId() != 0) || ($userdata->getAllowStoreId() != null)){
            $allowed_storeids =  explode(',', $userdata->getAllowStoreId());
        }
        else{
            $allowed_storeids = array(0);
        }

        if($this->getFilter('report_from') && $this->getFilter('report_to')){
            $storeIds = array();
            if ($this->getRequest()->getParam('store')) {
                $storeIds = array($this->getParam('store'));
            } elseif ($this->getRequest()->getParam('website')){
                $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            } elseif ($this->getRequest()->getParam('group')){
                $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            }
            // By default storeIds array contains only allowed stores
            $allowedStoreIds = array_keys(Mage::app()->getStores());
            //Filter Allowe Stores for this user
            if(!in_array(0, $allowed_storeids)){
               $allowedStoreIds = array_intersect($allowedStoreIds, $allowed_storeids);
            }
            // And then array_intersect with post data for prevent unauthorized stores reports
            $storeIds = array_intersect($allowedStoreIds, $storeIds);
            // If selected all websites or unauthorized stores use only allowed
            if (empty($storeIds)) {
                $storeIds = $allowedStoreIds;
            }
            // reset array keys
            $storeIds = array_values($storeIds);

            $from = date('Y-m-d'.' 00:00:00', strtotime($this->getFilter('report_from')));
            $to = date('Y-m-d'.' 23:59:59', strtotime($this->getFilter('report_to')));
            
            $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('store_id', array('in' => $storeIds));

            $deliveryfieldName          = 'shipping_delivery_date';
            $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
            $createfieldName            = 'created_at';
            $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
            $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
            $collection->getSelect()->where($newCondition);
            $this->setStatussummaryCollection($collection);
            return $collection;
        }
        return null;
    }

    protected function _prepareBetweenSql($fieldName, $from, $to)
    {
        // return sprintf('(%s BETWEEN %s AND %s)',
        //     $fieldName,
        //     Mage::getSingleton('core/resource')->getConnection()->quote($from),
        //     Mage::getSingleton('core/resource')->getConnection()->quote($to)
        // );
        $fromdate = "'".$from."'";
        $todate = "'".$to."'";
        return sprintf('(%s BETWEEN %s AND %s)',$fieldName,$fromdate,$todate);
    }

    protected function getFilteredData($collection, $field, $value)
    {        
        return $collection->addFieldToFilter((string) $field, (string) $value);
        
    }
    protected function getFilteredTotalData($collection, $field, $value)
    {        
        $collection->addFieldToFilter((string) $field, (string) $value);
        $collection->getSelect()
                ->columns('SUM(base_grand_total) as total')
                ->group($field);
        if($collection->count()){
            foreach ($collection as $key => $value) {
              return Mage::helper('core')->currency($value->getTotal(),true,false);
            }            
        }else{
            return Mage::helper('core')->currency(0,true,false);
        }
    }
}
