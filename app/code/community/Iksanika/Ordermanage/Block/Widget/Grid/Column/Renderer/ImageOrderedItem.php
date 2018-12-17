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

class Iksanika_Ordermanage_Block_Widget_Grid_Column_Renderer_ImageOrderedItem   
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $showImagesUrl = null;
    protected static $showByDefault = null;
    
    protected static $imagesWidth = null;
    protected static $imagesHeight = null;
    protected static $imagesScale = null;
    
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    
    protected static function initSettings()
    {
        if(!self::$showImagesUrl)
            self::$showImagesUrl = (int)Mage::getStoreConfig('ordermanage/images/showurl') === 1;
        if(!self::$showByDefault)
            self::$showByDefault = (int)Mage::getStoreConfig('ordermanage/images/showbydefault') === 1;
        if(!self::$imagesWidth)
            self::$imagesWidth = Mage::getStoreConfig('ordermanage/images/width');
        if(!self::$imagesHeight)
            self::$imagesHeight = Mage::getStoreConfig('ordermanage/images/height');
        if(!self::$imagesScale)
            self::$imagesScale = Mage::getStoreConfig('ordermanage/images/scale');
    }
    
    protected function _getValue(Varien_Object $row)
    {
        self::initSettings();
        
        $noSelection    =   false;
        $dored          =   false;
        if ($getter = $this->getColumn()->getGetter())
        {
            $val = $row->$getter();
        }

//        $val = $val2 = $row->getData($this->getColumn()->getIndex());
        $imgIndex = 'small_image';
        
        $val = $val2 = $row->getProduct()->getData($imgIndex);
        $noSelection = ($val == 'no_selection' || $val == '') ? true : $noSelection;
        $url = Mage::helper('ordermanage')->getImageUrl($val);
        
        if(!Mage::helper('ordermanage')->getFileExists($val)) 
        {
          $dored = true;
          $val .= "[*]";
        }
        
        $dored = (strpos($val, "placeholder/")) ? true : $dored;
        $filename = (!self::$showImagesUrl) ? '' : substr($val2, strrpos($val2, "/")+1, strlen($val2)-strrpos($val2, "/")-1);
        
        $val = ($dored) ? 
                ("<span style=\"color:red\" id=\"img\">$filename</span>") :
                "<span>". $filename ."</span>";
        
        $out = (!$noSelection) ? 
                ($val. '<center><a href="#" onclick="window.open(\''. $url .'\', \''. $val2 .'\')" title="'. $val2 .'" '. ' url="'.$url.'" id="imageurl">') :
                '';

        $outImagesWidth = self::$imagesWidth ? "width='".self::$imagesWidth."'":'';
        if(self::$imagesScale)
            $outImagesHeight = (self::$imagesHeight) ? "height='".self::$imagesHeight."'":'';
        else
            $outImagesHeight = (self::$imagesHeight && !self::$imagesWidth) ? "height='".self::$imagesHeight."'":'';

        //return '<img src="'.(Mage::helper("catalog/image")->init($productItem, "small_image")->resize(self::$imagesWidth)).'" '.$outImagesWidth.' '.$outImagesHeight.' alt="" />';
        try {

            $img  = Mage::helper("catalog/image")->init($row->getProduct(), $imgIndex);
            $imgR = $img->resize(self::$imagesWidth);
            $out .= (!$noSelection) ?
                    "<img src=".$imgR." ".$outImagesWidth." ".$outImagesHeight." border=\"0\"/>" :
                    "<center><strong>[".__('NO IMAGE')."]</strong></center>";
        }catch(Exception $e)
        {
            $out .= "<center><strong>[".__('NO IMAGE')."]</strong></center>";
        }
        
        return $out. ((!$noSelection)? '</a></center>' : '');
    }
    
}
