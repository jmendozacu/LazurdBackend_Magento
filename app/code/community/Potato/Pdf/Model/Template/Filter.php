<?php

class Potato_Pdf_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
    const CONSTRUCTION_FOREACH_PATTERN = '/{{foreach\s*(.*?)}}(.*?){{\\/foreach\s*}}/si';
    const PRODUCT_SMALL_IMG_SIZE = 135;
    const PRODUCT_SMALL_THUMBNAIL_SIZE = 75;

    /**
     * Setup callbacks for filters
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_modifiers['currency'] = array($this, 'modifierCurrency');
        $this->_modifiers['product_small_image'] = array($this, 'modifierProductSmallImg');
        $this->_modifiers['product_thumbnail'] = array($this, 'modifierProductThumbnail');
    }

    public function modifierProductSmallImg($value)
    {
        if (!$value instanceof Mage_Catalog_Model_Product) {
            return '';
        }
        return Mage::helper('catalog/image')->init($value, 'small_image')->resize(self::PRODUCT_SMALL_IMG_SIZE);
    }

    public function modifierProductThumbnail($value)
    {
        if (!$value instanceof Mage_Catalog_Model_Product) {
            return '';
        }
        return Mage::helper('catalog/image')->init($value, 'thumbnail')->resize(self::PRODUCT_SMALL_THUMBNAIL_SIZE);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function modifierCurrency($value)
    {
        return Mage::helper('core')->currency($value, true, false);
    }

    /**
     * @param array $construction
     *
     * @return string
     */
    public function foreachDirective($construction)
    {
        if (!is_array($construction) || !array_key_exists(2, $construction) || empty($construction[2])) {
            return '';
        }
        $container = $this->_getVariable($construction[1]);
        if (!is_array($container) && !$container instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) {
            return '';
        }
        //store "item" variable if exists
        $itemVal = null;
        if (array_key_exists('item', $this->_templateVars)) {
            $itemVal = $this->_templateVars['item'];
        }
        $result = '';
        foreach ($container as $item) {
            $this->_templateVars['item'] = $item;
            $result .= $this->filter($construction[2]);
        }
        $this->_templateVars['item'] = $itemVal;
        if (null === $itemVal) {
            unset($this->_templateVars['item']);
        }
        return $result;
    }

    /**
     * Filter the string as template.
     * Rewrited for logging exceptions
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        try {
            $value = $this->_filter($value);
        } catch (Exception $e) {
            $value = '';
            Mage::logException($e);
        }
        return $value;
    }

    /**
     * Filter the string as template. From Varien_Filter_Template magento 1901
     *
     * @param $value
     *
     * @return mixed
     * @throws Exception
     */
    protected function _filter($value)
    {
        // "depend" and "if" and "for" operands should be first
        foreach (array(
                     Varien_Filter_Template::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
                     Varien_Filter_Template::CONSTRUCTION_IF_PATTERN     => 'ifDirective',
                     self::CONSTRUCTION_FOREACH_PATTERN                  => 'foreachDirective',
                 ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $construction) {
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $construction) {
                $callback = array($this, $construction[1].'Directive');
                if(!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }
}