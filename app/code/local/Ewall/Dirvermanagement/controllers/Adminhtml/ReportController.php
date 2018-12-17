<?php

class Ewall_Dirvermanagement_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
			$this->loadLayout()->_setActiveMenu("dirvermanagement/driver")->_addBreadcrumb(Mage::helper("adminhtml")->__("Order Delivery Manger"),Mage::helper("adminhtml")->__("Order Delivery Manger"));
			return $this;
	}
	public function indexAction()
	{
		$this->loadLayout();
		$this->_title($this->__("Drivers order handeling report"));
		$this->renderLayout();
	}
	public function gridAction()
	{
		$data = $this->getRequest()->getPost();

		if($data['form-date'] != '' || $data['to-date'] != '' || $data['delivery'] != '')
		{
		$datas = array('form-date' => $data['form-date'],
						'to-date' => $data['to-date'],
						'delivery' => $data['delivery']);
		Mage::getSingleton('admin/session')->setDriversTime($datas);
		}
		$this->_title($this->__("Order Delivery Manger"));
		$this->_initAction();
		$this->renderLayout();
	}
}