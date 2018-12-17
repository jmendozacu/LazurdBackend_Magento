<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Inventorysuccess_Block_Adminhtml_SupplyNeeds_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (!$this->getRequest()->getParam('top_filter')) {
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('entity_id', null);
        } else {
            $topFilter = $this->getRequest()->getParam('top_filter');
            $sort = $this->getRequest()->getParam('sort');
            $dir = $this->getRequest()->getParam('dir');

            $supplyNeedsService = Magestore_Coresuccess_Model_Service::supplyNeedsService();
            $collection = $supplyNeedsService->getProductSupplyNeedsCollection($topFilter, $sort, $dir);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('inventorysuccess')->__('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );


        $this->addColumn('name',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Name'),
                'align' => 'left',
                'index' => 'name'
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('inventorysuccess')->__('SKU'),
                'index' => 'sku'
            )
        );

        $this->addColumn(
            'avg_qty_ordered',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Qty. Sold/day'),
                'type' => 'number',
                'index' => 'avg_qty_ordered',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        );

        $this->addColumn(
            'total_sold',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Total Sold'),
                'type' => 'number',
                'index' => 'total_sold',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        );

        $this->addColumn(
            'current_qty',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Current Qty'),
                'type' => 'number',
                'index' => 'current_qty',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        );

        $this->addColumn(
            'availability_date',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Available Date'),
                'type' => 'date',
                'index' => 'availability_date',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            )
        );

        $this->addColumn(
            'supply_needs',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Supply Needs'),
                'type' => 'number',
                'index' => 'supply_needs',
                'filter_condition_callback' => array($this, '_filterNumberCallback')
            )
        );

        $this->addExportType('*/*/exportListProductsCsv', Mage::helper('inventorysuccess')->__('CSV'));
        $this->addExportType('*/*/exportListProductsXml', Mage::helper('inventorysuccess')->__('Excel XML'));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productgrid', array(
            '_current' => true,
            'id' => $this->getRequest()->getParam('id'),
            'store' => $this->getRequest()->getParam('store')
        ));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getXml()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $indexes = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<items>';
        foreach ($this->getCollection() as $item) {
            $item->setStockItem(null);
            $xml .= $item->toXml($indexes);
        }
        if ($this->getCountTotals()) {
            $xml .= $this->getTotals()->toXml($indexes);
        }
        $xml .= '</items>';
        return $xml;
    }
}
