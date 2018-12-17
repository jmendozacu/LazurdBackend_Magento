<?php

class Potato_Pdf_Model_Source_ModuleEnableValidator extends Mage_Core_Model_Config_Data
{
    const DISABLE_VALUE = 0;

    protected function _beforeSave()
    {
        if ($this->_useService()) {
            return parent::_beforeSave();
        }
        if ($this->_isEnabledExecFunction() && $this->_isWkhtmltopdfInstalled()) {
            return parent::_beforeSave();
        }
        $this->setValue(self::DISABLE_VALUE);
        return $this;
    }

    protected function _useService()
    {
        $groups = Mage::app()->getRequest()->getParam('groups', array());
        if (array_key_exists('general', $groups) &&
            array_key_exists('fields', $groups['general']) &&
            array_key_exists('use_service', $groups['general']['fields']) &&
            array_key_exists('value', $groups['general']['fields']['use_service'])
        ) {
            return (bool)$groups['general']['fields']['use_service']['value'];
        }
        return false;
    }

    protected function _isEnabledExecFunction()
    {
        if (!function_exists('exec')) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('po_pdf')->__('Can\'t enable module, please enable "exec" function')
            );
            return false;
        }
        return true;
    }

    protected function _isWkhtmltopdfInstalled()
    {
        $groups = Mage::app()->getRequest()->getParam('groups', array());
        if (array_key_exists('general', $groups) &&
            array_key_exists('fields', $groups['general']) &&
            array_key_exists('lib_path', $groups['general']['fields']) &&
            array_key_exists('value', $groups['general']['fields']['lib_path'])
        ) {
            $libPath = $groups['general']['fields']['lib_path']['value'];
            $result = array();
            $status = 0;
            exec($libPath . " -V 2>&1", $result, $status);
            if ($status == 0) {
                return true;
            }
        }
        Mage::getSingleton('adminhtml/session')->addNotice(
            Mage::helper('po_pdf')->__('Can\'t enable module, please install <a href="http://wkhtmltopdf.org/" target="_blank">"Wkhtmltopdf"</a>')
        );
        return false;
    }
}