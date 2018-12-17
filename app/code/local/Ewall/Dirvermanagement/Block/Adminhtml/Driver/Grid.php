<?php

class Ewall_Dirvermanagement_Block_Adminhtml_Driver_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("driverGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(array('o'=> 'admin_role'), "o.user_id = main_table.user_id" ,array('*'));
        $collection->addFieldToFilter('parent_id' , 5);
		$collection->getSelect()->joinLeft(array('oeu'=> 'driver_management'), "oeu.userid = main_table.user_id" ,array('*'));
        $this->setCollection($collection);
        return parent::_prepareCollection();

		}
		protected function _prepareColumns()
		{

		        $this->addColumn('user_id', array(
		            'header'    => Mage::helper('dirvermanagement')->__('User  ID'),
		            'width'     => 5,
		            'align'     => 'right',
		            'sortable'  => false,
		            'index'     => 'user_id'
		        ));

		         $this->addColumn('username', array(
		             'header'    => Mage::helper('adminhtml')->__('User Name'),
		             'index'     => 'username'
		         ));

		        $this->addColumn('firstname', array(
		            'header'    => Mage::helper('adminhtml')->__('First Name'),
		            'index'     => 'firstname'
		        ));

		        $this->addColumn('lastname', array(
		            'header'    => Mage::helper('adminhtml')->__('Last Name'),
		            'index'     => 'lastname'
		        ));
		        $this->addColumn('lastname', array(
		            'header'    => Mage::helper('adminhtml')->__('Last Name'),
		            'index'     => 'lastname'
		        ));
				$this->addColumn("mobile", array(
				"header" => Mage::helper("dirvermanagement")->__("Mobile Number "),
				"index" => "mobile",
				));
				$this->addColumn("unique_id", array(
				"header" => Mage::helper("dirvermanagement")->__("Mobile Unique Id "),
				"index" => "unique_id",
				));

			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			 return $this->getUrl("*/*/edit", array("user_id" => $row->getId()));
		}

		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_driver', array(
					 'label'=> Mage::helper('dirvermanagement')->__('Remove Driver'),
					 'url'  => $this->getUrl('*/adminhtml_driver/massRemove'),
					 'confirm' => Mage::helper('dirvermanagement')->__('Are you sure?')
				));
			return $this;
		}

		static public function getOptionArray7()
		{
            $data_array=array();
			$data_array[1]='Active';
			$data_array[0]='Inactive';
            return($data_array);
		}
		static public function getValueArray7()
		{
            $data_array=array();
			foreach(Ewall_Dirvermanagement_Block_Adminhtml_Driver_Grid::getOptionArray7() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}


}