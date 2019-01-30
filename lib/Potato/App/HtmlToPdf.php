<?php

class Potato_App_HtmlToPdf
{
    //const API_URL = 'http://pdf.app.potatocommerce.com:8080/htmltopdf';
    const API_URL = 'http://pdf.app.potatocommerce.com/htmltopdf';

    static function process($options)
    {
        $data = Zend_Json::encode(
            array(
                'options' => $options,
            )
        );
        $ch = curl_init(self::API_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        $_result = curl_exec($ch);
        Mage::log("data : " . $data, null, 'potato.log');
        Mage::log("result : " . $_result, null, 'potato.log');
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new Exception('Service not available');
        }
        if (curl_getinfo($ch, CURLINFO_CONTENT_TYPE) == 'application/json') {
            $json = json_decode($_result, true);
            throw new Exception($json['result']);
        }
        return $_result;
    }

    /**
     * @return array
     * @throws Exception
     */
    static function getOptimizedImages()
    {
        if (empty($_POST) || !array_key_exists('optimization_result', $_POST)) {
            throw new Exception('POST data is empty');
        }
        if (!is_string($_POST['optimization_result'])) {
            throw new Exception('String required');
        }
        if (!$images = json_decode($_POST['optimization_result'])) {
            throw new Exception('Not valid JSON data');
        }
        $collection = array();
        foreach ($images as $image) {
            array_push($collection, new Potato_App_Image_Result($image));
        }
        return $collection;
    }
}