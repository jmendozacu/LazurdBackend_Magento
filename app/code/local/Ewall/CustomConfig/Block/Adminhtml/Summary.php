<?php
 class Ewall_CustomConfig_Block_Adminhtml_Summary extends Mage_Adminhtml_Block_Template {
	
	public function __construct()
    {
        parent::__construct();
       	$this->setTemplate('ewall/customconfig/summary.phtml');
    }

    protected function _prepareLayout()
    {       
        $from = $this->getRequest()->getParam('report_from');
        $to   = $this->getRequest()->getParam('report_to');
        if(!$from || !$to){
            return $this;
        }
        Mage::register('delivery-filters', $this->getRequest()->getParams());
        parent::_prepareLayout();

    }
    protected function getFilter($param)
    {        
        if(Mage::registry('delivery-filters')){
            return Mage::registry('delivery-filters')[$param];            
        }
        return null;
    }

    public function getCollection()
    {   
        if(Mage::registry('delivery-filters')){
            $fromDate = date('Y-m-d'.' 00:00:00', strtotime($this->getFilter('report_from')));
            $toDate = date('Y-m-d'.' 23:59:59', strtotime($this->getFilter('report_to')));
            $collection = Mage::getResourceModel('customconfig/summary')->setDateRange($fromDate,$toDate);
            return $collection;           
        }
        return null;        
    }

    public function getFilteredData($field, $value)
    {        
        if($this->getCollection()){
            return $this->getCollection()->addFieldToFilter((string) $field, $value);
        }
    }
    
}