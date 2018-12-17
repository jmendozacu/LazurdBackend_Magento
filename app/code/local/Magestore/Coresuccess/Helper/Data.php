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

class Magestore_Coresuccess_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @var array
     */
    private $_ERPmoudles = array(
                                'inventorysuccess',
//                                'barcodesuccess'
                            );

    /**
     *
     * @var array
     */
    private $_unapply_ERPlayout = array();

    /**
     *
     * @return string
     */
    public function getCurrentModule() {
        return Mage::registry('current_real_module_name');
    }

    /**
     *
     * @return string
     */
    public function getCurrentModuleKey($moduleName = null) {
        $moduleName = $moduleName ? $moduleName : $this->getCurrentModule();
        $moduleName = str_replace('Magestore_', '', $moduleName);
        return strtolower($moduleName);
    }

    /**
     *
     * @return string
     */
    public function getCurrentModuleName() {
        $moduleName = $this->getCurrentModule();
        if (!$moduleName)
            return null;
        $aliasName = $this->getAppConfig($moduleName, 'alias_name');
        $moduleName = $aliasName ? $aliasName : $moduleName;
        $moduleName = str_replace('Magestore_', '', $moduleName);
        return $moduleName;
    }

    /**
     * Check if the module is an app of ERP Plus
     *
     * @return boolean
     */
    public function isERPmodule($moduleKey = null) {

        if($moduleKey) {
            $activeApps = $this->getActiveApps();
            if(isset($activeApps[$moduleKey])){
                return true;
            }
            return false;
        }

        if (in_array($this->getCurrentModuleKey(), $this->_ERPmoudles)) {
            return true;
        }
        if ((bool) $this->getAppConfig($this->getCurrentModule(), 'isERPmodule')) {
            return true;
        }
        return false;
    }

    /**
     * Get parent module
     *
     * @param string $moduleName
     * @return string
     */
    public function getDependModule($moduleName) {
        $appInfo = Mage::getConfig()->getModuleConfig($moduleName);
        if (isset($appInfo->depends)) {
            $depends = array_keys($appInfo->depends->asArray());
            if (count($depends)) {
                foreach ($depends as $depend) {
                    return $depend;
                }
            }
        }
        return null;
    }

    /**
     * Check if apply ERP layout to module
     *
     * @return boolean
     */
    public function isApplyERPlayout() {
        if (in_array($this->getCurrentModuleKey(), $this->_unapply_ERPlayout)) {
            return false;
        }
        return $this->isERPmodule();
    }

    /**
     *
     * @param string $app (Magestore_Coresuccess)
     * @param string $field
     * @return string
     */
    public function getAppConfig($app, $field) {
        $appInfo = Mage::getConfig()->getModuleConfig($app);
        if (isset($appInfo->erp)) {
            if (isset($appInfo->erp->$field))
                return (string) $appInfo->erp->$field;
        }
        return null;
    }

    /**
     *
     * @return array
     */
    public function getActiveApps() {
        $activeApps = array();
        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleInfo) {
            if ($moduleName === 'Mage_Adminhtml') {
                continue;
            }
            if ($moduleName === 'Magestore_Magenotification') {
                continue;
            }
            if (strpos('a' . $moduleName, 'Magestore') == 0) {
                continue;
            }

            $moduleKey = str_replace('magestore_', '', strtolower($moduleName));

            if (!(bool) $this->getAppConfig($moduleName, 'isERPmodule')) {
                if (!in_array($moduleKey, $this->_ERPmoudles)) {
                    continue;
                }
            }

            $activeApps[$moduleKey] = $moduleName;
        }
        return $activeApps;
    }

    /**
     * Update layout of inventory configuration page
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function updateConfigLayout($controller, $layout) {
        $fullRequest = $controller->getFullActionName();
        $section = $this->getCurrentSectionConfig();
        $applied = false;
        if ($fullRequest != 'adminhtml_system_config_edit')
            return;
        if (in_array($section, $this->_unapply_ERPlayout))
            return;
        if (in_array($section, $this->_ERPmoudles))
            $applied = true;
        if ((bool) $this->getAppConfig('Magestore_' . ucwords($section), 'isERPmodule')) {
            $applied = true;
        }
        if ($applied) {
            $layout->getUpdate()->addHandle('adminhtml_coresuccess_module_layout');
        }
    }

    /**
     *
     * @return string
     */
    public function getCurrentSectionConfig() {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'carriers') {
            if (Mage::app()->getRequest()->getParam('storepickup') == 1) {
                $section = 'storepickup';
            }
        }
        return $section;
    }

}
