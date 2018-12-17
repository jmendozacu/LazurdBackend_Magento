<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Driver_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("driver_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("dirvermanagement")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("dirvermanagement")->__("Item Information"),
				"title" => Mage::helper("dirvermanagement")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("dirvermanagement/adminhtml_driver_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
