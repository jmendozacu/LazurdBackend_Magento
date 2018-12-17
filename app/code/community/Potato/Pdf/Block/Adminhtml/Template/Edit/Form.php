<?php

class Potato_Pdf_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'po_pdf/adminhtml/wysiwyg/tiny_mce/plugins/pdf_preview/editor_plugin_src.js');
        }
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $form = new Varien_Data_Form(
            array(
                'id'     => 'edit_form',
                'action' => Mage::helper('adminhtml')->getUrl(
                        '*/*/save',
                        array('id' => $id)
                    ),
                'method' => 'post',
            )
        );

        //prepare Load Default Template field set
        $default = $form->addFieldset('default',
            array(
                'legend' => $this->__('Load Default Template'),
                'class'  => 'fieldset-wide'
            )
        );
        $default->addField(
            'template',
            'select',
            array(
                'label'   => $this->__('Template'),
                'name'    => 'template',
                'type'    => 'select',
                'options' => Mage::getModel('po_pdf/source_template')->getLocalTemplates(true),
            )
        );
        $default->addField(
            'locale',
            'select',
            array(
                'label'   => $this->__('Locale'),
                'name'    => 'locale',
                'type'    => 'select',
                'options' => Potato_Pdf_Helper_Data::getOptionLocales(),
            )
        );
        $default->addField(
            'load_template_btn',
            'button',
            array(
                'class' => 'form-button',
                'type'  => 'button',
                'onclick' => 'PdfDefaultTemplate.getRequest()'
            )
        );

        //prepare General Information field set
        $general = $form->addFieldset('general',
            array(
                'legend' => $this->__('General Information'),
                'class'  => 'fieldset-wide'
            )
        );
        $general->addField(
            'title',
            'text',
            array(
                'label'    => $this->__('Title'),
                'name'     => 'title',
                'type'     => 'text',
                'required' => true,
            )
        );
        $general->addField('content',
            'editor',
            array(
                'name'      => 'content',
                'label'     => $this->__('Content'),
                'title'     => $this->__('Content'),
                'style'     => 'height:36em',
                'required'  => true,
                'config'    => $this->_getWysiwygConfig()
            )
        );

        //set form values
        $template = Mage::registry('current_template');
        $template->setLoadTemplateBtn($this->__('Load Template'));
        $template->setLocale(Mage::app()->getLocale()->getLocaleCode());

        $form->setValues($template->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _getWysiwygConfig()
    {
        //default config
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        //remove all plugins
        $config->setPlugins(array());

        //prepare variable plugin
        $variablesPlugin = Mage::getModel('core/variable_config')->getWysiwygPluginSettings($config);
        $onclickParts = array(
            'search'  => array('html_id'),
            'subject' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl() . '\', \'{{html_id}}\');'
        );
        $variablesPlugin['plugins'][0]['options']['url'] = $this->getVariablesWysiwygActionUrl();
        $variablesPlugin['plugins'][0]['options']['onclick'] = $onclickParts;
        $config->addData($variablesPlugin);

        $config->setEnabled(false);
        $config->setIsHidden(true);
        $config->setPlugins($this->addAdditionPlugins($config));
        return $config;
    }

    public function getVariablesWysiwygActionUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/potato_pdf_template_variable/wysiwygPlugin');
    }

    public function addAdditionPlugins($config)
    {
        $variableConfig = array();
        $variableWysiwygPlugin = array(
            //PDF preview plugin
            array('name' => 'pdfpreview',
                  'src' => Mage::getBaseUrl('js') . 'po_pdf/adminhtml/wysiwyg/tiny_mce/plugins/pdf_preview/editor_plugin_src.js',
                  'options' => array(
                      'title'                  => $this->__('Preview'),
                      'plugin_preview_pageurl' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/potato_pdf_template/preview'),
                      'class'                  => 'pdfpreview plugin',
                      'onclick'                => 'window.PdfPreview(\'' . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/potato_pdf_template/preview') . '\')',
                  )
            ),

            //Full page plugin for support full page structure
            array(
                'name' => 'fullpage',
                'src'  => Mage::getBaseUrl('js') . 'tiny_mce/plugins/fullpage/editor_plugin.js'
            )
        );
        $configPlugins = $config->getData('plugins');
        $variableConfig['plugins'] = array_merge($configPlugins, $variableWysiwygPlugin);
        return $variableConfig['plugins'];
    }
}