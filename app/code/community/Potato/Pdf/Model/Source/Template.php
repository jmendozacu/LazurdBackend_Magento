<?php

class Potato_Pdf_Model_Source_Template extends Varien_Object
{
    /**
     * Config xpath to pdf template node
     *
     */
    const XML_PATH_TEMPLATE_PDF = 'global/template/pdf';

    /**
     * Generate list of pdf templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        //add empty value
        $options = array(
            array(
                'value' => '',
                'label' => Mage::helper('po_pdf')->__('Do not use')
            )
        );

        //add saved templates
        if(!$collection = Mage::registry('config_system_pdf_template')) {
            $collection = Mage::getResourceModel('po_pdf/template_collection')->load();
            Mage::register('config_system_pdf_template', $collection);
        }

        foreach ($collection->toOptionHash() as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        //add local template for current option
        $templateName = Mage::helper('po_pdf')->__('Default Template from Locale');
        $nodeName = str_replace('/', '_', $this->getPath());
        $templateLabelNode = Mage::app()->getConfig()->getNode(self::XML_PATH_TEMPLATE_PDF . '/' . $nodeName . '/label');

        if ($templateLabelNode) {
            $templateName = Mage::helper('po_pdf')->__((string)$templateLabelNode);
            $templateName = Mage::helper('po_pdf')->__('%s (Default Template from Locale)', $templateName);
        }
        array_unshift(
            $options,
            array(
                'value' => $nodeName,
                'label' => $templateName
            )
        );
        return $options;
    }

    /**
     * @param bool $withEmptyFlag
     *
     * @return array
     */
    static function getLocalTemplates($withEmptyFlag = true)
    {
        $options = array();
        if ($withEmptyFlag) {
            $options = array(Mage::helper('po_pdf')->__('Blank'));
        }
        foreach (Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_PDF)->asArray() as $key => $node) {
            $options[$key] = Mage::helper('po_pdf')->__((string)$node['label']);
        }
        return $options;
    }

    /**
     * @return array
     */
    static function getLocalTemplatesFiles()
    {
        $options = array();
        foreach (Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_PDF)->asArray() as $key => $node) {
            $options[$key] = $node['file'];
        }
        return $options;
    }
}