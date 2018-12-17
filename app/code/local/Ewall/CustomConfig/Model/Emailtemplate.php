<?php
class Ewall_CustomConfig_Model_Emailtemplate
{
  /**
   * Provide available options as a value/label array
   *
   * @return array
   */
  const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';
  
  public function toOptionArray()
  {
    $result = array();
    $collection = Mage::getResourceModel('core/email_template_collection')
        ->load();
    $options = $collection->toOptionArray();
    $defOptions = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
    foreach ($defOptions as $v) {
        $options[] = $v;
    }
    foreach ($options as $v) {
        $result[$v['value']] = $v['label'];
    }
    // sort by names alphabetically
    asort($result);
    if (!$asHash) {
        $options = array();
        $options[] = array('value' => '', 'label' => '---------Choose Email Template---------');
        foreach ($result as $k => $v) {
            if ($k == '')
                continue;
            $options[] = array('value' => $k, 'label' => $v);
        }
        $result = $options;
    }
    return $result;
  }
}

?>