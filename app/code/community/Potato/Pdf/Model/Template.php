<?php

class Potato_Pdf_Model_Template extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('po_pdf/template');
    }

    /**
     * @param      $id
     * @param null $field
     *
     * @return $this
     */
    public function load($id, $field=null)
    {
        if ((int)$id) {
            return parent::load($id, $field);
        }
        $this->setContent($this->loadDefault($id));
        return $this;
    }

    /**
     * @param array $variables
     * @param bool  $emulateStoreDesign
     *
     * @return mixed
     */
    public function getProcessedTemplate($variables = array(), $emulateStoreDesign = false)
    {
        if ($emulateStoreDesign && array_key_exists('store_id', $variables)) {
            //start emulate design
            $appEmulation = Potato_Pdf_Helper_Data::startEmulation($variables['store_id']);
        }
        $template = Mage::getModel('core/email_template');
        $filter = Mage::getSingleton('po_pdf/template_filter');
        $template->setTemplateFilter($filter);
        $template->setTemplateText($this->getContent());

        $result = $template->getProcessedTemplate($variables);

        if (isset($appEmulation)) {
            //end emulate design
            Potato_Pdf_Helper_Data::stopEmulation($appEmulation);
        }
        return $result;
    }

    /**
     * @param      $templateId
     * @param null $locale
     *
     * @return string
     */
    public function loadDefault($templateId, $locale=null)
    {
        $defaultTemplates = Potato_Pdf_Model_Source_Template::getLocalTemplatesFiles();
        if (!isset($defaultTemplates[$templateId])) {
            return '';
        }
        $templateText = Mage::app()->getTranslator()->getTemplateFile(
            $defaultTemplates[$templateId], 'pdf', $locale
        );
        return $templateText;
    }
}