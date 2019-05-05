<?php

class Cac_Restapi_Kitchen_Model_Api2_Kitchen_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    const OPERATION_GET_KITCHEN_CATS = 'cats';
    const OPERATION_GET_KITCHEN_TODAY_DELIVERY = 'today';
    const OPERATION_GET_KITCHEN_PRODUCTS_BY_CAT_AND_DATES = 'products';
    const OPERATION_GET_KITCHEN_ITEMS_BY_PRODUCT_AND_DATES = 'items';
    const OPERATION_SET_KITCHEN_ITEMS_READY = 'ready';
    // /cac/kitchen/cats
    public function getَAllCats()
    {
//        $categories = Mage::getModel('catalog/category')
//            ->getCollection()
//            ->addAttributeToSelect('name') //you can add more attributes using this
//            ->addAttributeToFilter('entity_id', array('in'=>array(1,2,3)));
//
//        foreach($categories as $_cat){
//            $holder[]= $_cat->getName();
//        }
//       return $holder;


        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', 1) //only active categories
            ->addAttributeToFilter('include_in_menu', 1); //only categories included in menu


        foreach ($collection as $category) {
            if ($category->getLevel() == 2) {
                $cat["id"]= $category->getData("entity_id");
                $cat["name"]= $category->getName();
                $holder[] =$cat;
            }
        }
        return $holder;
    }

    // /cac/kitchen/today
    public function getTodayDeliveries()
    {
        $cat_id = $this->getRequest()->getParam('cat_id');
        $date = $this->getRequest()->getParam('date');

        list($year_s,$month_s,$day_s)=explode("/",$date);
        list($year_e,$month_e,$day_e)=explode("/",$date);

        if (strlen($month_s)==1)
            $month_s="0" . $month_s;
        if (strlen($day_s)==1)
            $day_s="0" . $day_s;

        if (strlen($month_e)==1)
            $month_e="0" . $month_e;
        if (strlen($day_e)==1)
            $day_e="0" . $day_e;


        $total_query="SELECT distinct increment_id as order_id,shipping_delivery_date as delivery_date,order_status as order_status ".
            "FROM sales_flat_order as sfo join sales_flat_order_item as sfoi on sfo.entity_id=sfoi.order_id ".
            "where status!='canceled' and (sfo.shipping_delivery_date >= '$year_s-$month_s-$day_s 0:0' AND sfo.shipping_delivery_date <= '$year_e-$month_e-$day_e 23:59') and find_in_set($cat_id,category_id)>0 order by shipping_delivery_date";

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($total_query);
        if ($results==null)
            return [];
        else
            return $results;
    }

    public function getProductsByCatAndDates()
    {
        $cat_id = $this->getRequest()->getParam('cat_id');
        $start_date = $this->getRequest()->getParam('start_date');
        $end_date = $this->getRequest()->getParam('end_date');
        list($year_s,$month_s,$day_s)=explode("/",$start_date);
        list($year_e,$month_e,$day_e)=explode("/",$end_date);

        if (strlen($month_s)==1)
            $month_s="0" . $month_s;
        if (strlen($day_s)==1)
            $day_s="0" . $day_s;

        if (strlen($month_e)==1)
            $month_e="0" . $month_e;
        if (strlen($day_e)==1)
            $day_e="0" . $day_e;

        $query="select sfoi.product_id,name,count(name) as count from sales_flat_order as sfo join sales_flat_order_item as sfoi on sfo.entity_id=sfoi.order_id".
            " join catalog_category_product as ccp on ccp.product_id=sfoi.product_id ".
            " where ccp.category_id=$cat_id and (sfo.shipping_delivery_date >= '$year_s-$month_s-$day_s 0:0' AND sfo.shipping_delivery_date <= '$year_e-$month_e-$day_e 23:59') ".
            "group by name order by name";
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($query);
        if ($results==null)
            return [];
        else
            return $results;

    }
    public function getItemsByProductAndDates()
    {
        $cat_id = $this->getRequest()->getParam('cat_id');
        $product_id = $this->getRequest()->getParam('product_id');
        $start_date = $this->getRequest()->getParam('start_date');
        $end_date = $this->getRequest()->getParam('end_date');
        list($year_s,$month_s,$day_s)=explode("/",$start_date);
        list($year_e,$month_e,$day_e)=explode("/",$end_date);

        if (strlen($month_s)==1)
            $month_s="0" . $month_s;
        if (strlen($day_s)==1)
            $day_s="0" . $day_s;

        if (strlen($month_e)==1)
            $month_e="0" . $month_e;
        if (strlen($day_e)==1)
            $day_e="0" . $day_e;

        $filter_products="";
        if (isset($product_id) && $product_id!=-1)
            $filter_products="and sfoi.product_id=$product_id";

        $category_filter = $cat_id > 0 ? "ccp.category_id=$cat_id and " : "";

        $query="select sfo.increment_id as order_id, sfoi.item_id,sfoi.item_status,sfoi.product_id,name as product_name,qty_ordered as quantity,shipping_delivery_date,sfoi.text_custom_options_value as custom_text ".
            " from sales_flat_order as sfo join sales_flat_order_item as sfoi on sfo.entity_id=sfoi.order_id join catalog_category_product as ccp on ccp.product_id=sfoi.product_id  ".
            "where {$category_filter} (sfo.shipping_delivery_date >= '$year_s-$month_s-$day_s 0:0' AND sfo.shipping_delivery_date <= '$year_e-$month_e-$day_e 23:59') ".
            "$filter_products order by name";


        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->fetchAll($query);
        if ($results==null)
            return [];
        else
            return $results;
    }

    public function setItemReady()
    {
        $item_id = $this->getRequest()->getParam('item_id');
        $ready = strtolower($this->getRequest()->getParam('ready')) == 'true';
        if ($ready==true) {
            $status = "ready";
            $query = "update sales_flat_order_item set item_status='$status' where item_id=$item_id";
        }
        else {
            $query = "update sales_flat_order_item set item_status=NULL where item_id=$item_id";
        }

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $results = $readConnection->query($query);
        if ($results==null) {
            $error["error"]="error while setting ready";
            return $error;
        }
        else {
            // find parent order id
            $query_i="select order_id from sales_flat_order_item where item_id=$item_id limit 1";
            $order_id=$readConnection->fetchCol($query_i)[0];
            // check if all items are ready, then set order status to ready
            $query_c="select count(*) from sales_flat_order_item where order_id=$order_id and COALESCE(item_status,'')!='ready'";
            $count=$readConnection->fetchCol($query_c)[0];
            $result["item_ready"]=$ready;
            if ($count==0)
            {
                // no left over order items, then set whole order status to ready
                $query_o="update sales_flat_order set order_status='ready' where entity_id=$order_id";
                $readConnection->query($query_o);
                $result["order_id"]=(int)$order_id;
                $result["order_ready"]=true;
            }
            else
                {
                    // no left over order items, then set whole order status to ready
                    $query_o="update sales_flat_order set order_status='pending' where entity_id=$order_id";
                    $readConnection->query($query_o);
                    $result["order_id"]=(int)$order_id;
                    $result["order_ready"]=false;
                }
            return $result;
        }
    }


    /**
     * Action Dispatcher
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_KITCHEN_CATS:
                $result = $this->getَAllCats();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_KITCHEN_TODAY_DELIVERY:
                $result = $this->getTodayDeliveries();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_KITCHEN_PRODUCTS_BY_CAT_AND_DATES:
                $result = $this->getProductsByCatAndDates();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_KITCHEN_ITEMS_BY_PRODUCT_AND_DATES:
                $result = $this->getItemsByProductAndDates();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_SET_KITCHEN_ITEMS_READY:
                $result = $this->setItemReady();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;


        }
    }


}