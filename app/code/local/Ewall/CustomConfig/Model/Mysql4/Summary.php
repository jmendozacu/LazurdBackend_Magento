<?php
class Ewall_CustomConfig_Model_Mysql4_Summary extends Mage_Sales_Model_Resource_Order_Collection
{
    public function setDateRange($from, $to)
    {

        // $adapter                    = $this->getConnection();
        // $deliveryfieldName          = 'shipping_arrival_date';
        // $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
        // $createfieldName            = 'created_at';
        // $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
        // //$Condition = $adapter->quoteInto("order.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED);
        // $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
        
        // // $columns = array('total_delivery_count' => 'COUNT(order.entity_id)');
        // // $fieldName = 'order.store_id';
        // // $allStores = Mage::app()->getStores();
        // // foreach ($allStores as $_eachStoreId => $val) 
        // // {
        // //     $_store = Mage::app()->getStore($_eachStoreId)->getId();
        // //     $columns['total_delivery_store_'.$_store.'_count'] = new Zend_Db_Expr("COUNT(CASE order.entity_id WHEN $fieldName=$_store THEN 1 ELSE 0 END)");
        // // }

        // // $Timeslots = Mage::getModel('deliverydate/deliverydate')->getCollection()->setOrder('id','asc');                
        // // if(count($Timeslots)):
        // //     $fieldName = 'order.shipping_arrival_time_slot';
        // //     foreach ($Timeslots as $key => $value) { 
        // //         $id = $value->getId();
        // //         $columns['total_delivery_slot_'.$id.'_count'] = new Zend_Db_Expr("COUNT(CASE order.entity_id WHEN $fieldName= $id THEN 1 ELSE 0 END)");
        // //     }
        // // endif;
        // $columns = array('order.entity_id','order.store_id','order.shipping_arrival_time_slot');

        // $this->getSelect() //->reset()
        //     //->from(
        //         //array('order' => $this->getTable('sales/order')),$columns
        //         //)
        //      ->where($newCondition);
        // return $this;
        $adapter                    = $this->getConnection();
        $deliveryfieldName          = 'shipping_delivery_date';
        $orderdeliveryJoinCondition = $this->_prepareBetweenSql($deliveryfieldName, $from, $to);
        $createfieldName            = 'created_at';
        $ordercreateJoinCondition = $this->_prepareBetweenSql($createfieldName, $from, $to);
        $newCondition =  new Zend_Db_Expr("CASE WHEN $deliveryfieldName IS NULL THEN $ordercreateJoinCondition ELSE $orderdeliveryJoinCondition END");
        $this->getSelect()->where($newCondition);
        return $this;
    }

    protected function _prepareBetweenSql($fieldName, $from, $to)
    {
        return sprintf('(%s BETWEEN %s AND %s)',
            $fieldName,
            $this->getConnection()->quote($from),
            $this->getConnection()->quote($to)
        );
    }
}
