<?php

class Potato_Pdf_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('po_pdfTemplateGrid');
        $this->setDefaultSort('main_table.id', 'desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('po_pdf/template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('ID'),
                'index'  => 'id',
                'type'   => 'number',
                'width'  => 100
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'  => $this->__('Title'),
                'align'   => 'left',
                'index'   => 'title',
                'type'    => 'text'
            )
        );
        $this->addColumn('action',
            array(
                'header'    => $this->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'url'     => array(
                            'base' => '*/*/edit'
                        ),
                        'field' => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            )
        );
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'   => $this->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete'),
                'confirm' => $this->__('Are you sure?')
            )
        );
    }
}