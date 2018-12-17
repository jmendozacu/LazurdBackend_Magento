<?php
class Custom_Restapi_Countryregions_Model_Api2_Countryregion_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * retrieve products by category
     * @return array
     */

    public function _create() {

    }

     /**
     * Retrieve a Categoies
     * @return string
     */

    public function _retrieve()
    {
      
      $countryId = 'KW';
      $countryCollection = Mage::getModel('directory/country')->getCollection();
      foreach ($countryCollection as $country) {
          if ($countryId == $country->getCountryId()) {
              $countryId = $country->getCountryId();
              $countryCode = $country->getCountryCode();
              break;
          }
      }
      $regionCollection = Mage::getModel('directory/region_api')->items($countryId);
      $countryCollection = null;
      echo json_encode($regionCollection);
      exit(); 

    }

}
?>