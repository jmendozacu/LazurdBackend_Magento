<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Ordermanage_Block_Widget_Grid_Column 
    extends Mage_Adminhtml_Block_Widget_Grid_Column
{
    public function getRenderer()
    {
        $exportMode = Mage::helper('ordermanage')->isExportMode();
//        echo $exportMode ? 'true' : 'false';
        if (!$this->_renderer) {
            $rendererClass = $this->getData('renderer');
            if (!$rendererClass || $exportMode) {
                $rendererClass = $this->_getRendererByType();
            }
            $this->_renderer = $this->getLayout()->createBlock($rendererClass)
                ->setColumn($this);
        }
        return $this->_renderer;
    }

    protected function _getRendererByType()
    {
        $exportMode = Mage::helper('ordermanage')->isExportMode();
        
        $type = strtolower($this->getType());
        $renderers = $this->getGrid()->getColumnRenderers();

        if (is_array($renderers) && isset($renderers[$type])) {
            return $renderers[$type];
        }

        if($exportMode)
        {
            if($type == 'input' || $type == 'select')
                $type = 'text';
        }
        
        switch ($type) {
            case 'date':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_date';
                break;
            case 'datetime':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_datetime';
                break;
            case 'number':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_number';
                break;
            case 'currency':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_currency';
                break;
            case 'price':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_price';
                break;
            case 'country':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_country';
                break;
            case 'concat':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_concat';
                break;
            case 'action':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_action';
                break;
            case 'options':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_options';
                break;
            case 'checkbox':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_checkbox';
                break;
            case 'massaction':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_massaction';
                break;
            case 'radio':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_radio';
                break;
            case 'input':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_input';
                break;
            case 'select':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_select';
                break;
            case 'text':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_longtext';
                break;
            case 'store':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_store';
                break;
            case 'wrapline':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_wrapline';
                break;
            case 'theme':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_theme';
                break;
            default:
                $rendererClass = 'adminhtml/widget_grid_column_renderer_text';
                break;
        }
        return $rendererClass;
    }

    
}