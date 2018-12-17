<?php

class Potato_Pdf_Helper_Data extends Mage_Core_Helper_Abstract
{
    const TMP_FOLDER = 'po_pdf';

    /**
     * @param $storeId
     * @param $area
     *
     * @return Varien_Object
     */
    static function startEmulation($storeId, $area = Mage_Core_Model_App_Area::AREA_FRONTEND)
    {
        if (class_exists('Mage_Core_Model_App_Emulation')) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $emulateInfo = $appEmulation->startEnvironmentEmulation($storeId);
        } else {
            $emulateInfo = new Varien_Object;
            $emulateInfo->setStoreId(Mage::app()->getStore()->getId());
            Mage::app()->setCurrentStore($storeId);
            $initialDesign = Mage::getDesign()->setAllGetOld(array(
                    'package' => Mage::getStoreConfig('design/package/name', $storeId),
                    'store'   => $storeId,
                    'area'    => $area
                ));
            $emulateInfo->setDesign($initialDesign);
            Mage::getDesign()->setTheme('');
            Mage::getDesign()->setPackageName('');
        }

        return $emulateInfo;
    }

    /**
     * @param Varien_Object $emulateInfo
     *
     * @return bool
     */
    static function stopEmulation(Varien_Object $emulateInfo)
    {
        if (class_exists('Mage_Core_Model_App_Emulation')) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $appEmulation->stopEnvironmentEmulation($emulateInfo);
        } else {
            Mage::app()->setCurrentStore($emulateInfo->getStoreId());
            Mage::getDesign()->setAllGetOld($emulateInfo->getDesign());
            Mage::getDesign()->setTheme('');
            Mage::getDesign()->setPackageName('');
        }
        return true;
    }

    /**
     * @param      $htmlList
     * @param null $store
     *
     * @return bool|string
     */
    static function convertHtmlToPdf($htmlList, $store = null)
    {
        if (!is_array($htmlList) || empty($htmlList)) {
            return false;
        }
        $htmlFiles = array();
        $optionConvertedFiles = '';
        $options = array(
            'margin_left' => Potato_Pdf_Helper_Config::getMarginLeft($store),
            'margin_right' => Potato_Pdf_Helper_Config::getMarginRight($store),
            'margin_bottom' => Potato_Pdf_Helper_Config::getMarginBottom($store),
            'orientation' => Potato_Pdf_Helper_Config::getPageOrientation($store),
            'margin_top' => Potato_Pdf_Helper_Config::getMarginTop($store),
            'format' => Potato_Pdf_Helper_Config::getPageFormat($store),
            'additional' => Potato_Pdf_Helper_Config::getAdditionalOptions(),
            'html' => array()
        );
        foreach ($htmlList as $html) {
            //create temp html file
            $filename = md5($html) . '.html';
            $htmlFilePath = self::getPdfTmpDir() . DS . $filename;
            file_put_contents($htmlFilePath,  chr(239) . chr(187) . chr(191) . $html);
            $_htmlUrl = self::getPdfTmpUrl($filename);
            $options['html'][] = $_htmlUrl;
            $optionConvertedFiles .= ' ' . $_htmlUrl . ' ';
            array_push($htmlFiles, $htmlFilePath);
        }
        //create temp pdf file
        $pdfFilePath = self::getPdfTmpDir() . DS . md5($html) . '.pdf';
        $result = array();
        $status = 0;
        $pdfContent = false;

        //prepare options
        $optionsLine = ' -L ' . $options['margin_left'] . 'pt'
            . ' -R ' . $options['margin_right'] . 'pt'
            . ' -T ' . $options['margin_top'] . 'pt'
            . ' -B ' . $options['margin_bottom'] . 'pt'
            . ' -O ' . $options['orientation']
            . ' -s ' . $options['format']
            . ' ' . $options['additional']
        ;
        if (Potato_Pdf_Helper_Config::useService($store)) {
            $_result = Potato_App_HtmlToPdf::process($options);
            file_put_contents($pdfFilePath, $_result);
        } else {
            exec(Potato_Pdf_Helper_Config::getLibPath() . $optionsLine . $optionConvertedFiles . $pdfFilePath . ' 2>&1',
                $result,
                $status
            );
        }

        if (file_exists($pdfFilePath)) {
            //remove tmp pdf file
            $pdfContent = file_get_contents($pdfFilePath);
            unlink($pdfFilePath);
        } else {
            Mage::log(print_r($result,true), 1, 'po_pdf.log', true);
        }

        //remove tmp html file
        foreach ($htmlFiles as $htmlFilePath) {
            unlink($htmlFilePath);
        }
        return $pdfContent;
    }

    /**
     * @return string
     */
    static function getPdfTmpDir()
    {
        //prepare tmp folder
        Mage::getConfig()->createDirIfNotExists(Mage::getBaseDir('media') . DS . self::TMP_FOLDER);
        return Mage::getBaseDir('media') . DS . self::TMP_FOLDER;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    static function getPdfTmpUrl($filename)
    {
        return Mage::getBaseUrl('media') . self::TMP_FOLDER . '/'. $filename;
    }

    /**
     * @return array
     */
    static function getOptionLocales()
    {
        $options = array();
        foreach (Mage::app()->getLocale()->getOptionLocales() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    static function getFileName($prefix='')
    {
        return $prefix . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf';
    }
}