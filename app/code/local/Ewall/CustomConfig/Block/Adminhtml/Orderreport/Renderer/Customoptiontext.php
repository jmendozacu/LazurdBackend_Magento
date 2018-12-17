<?php
class Ewall_CustomConfig_Block_Adminhtml_Orderreport_Renderer_Customoptiontext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
     
    public function render(Varien_Object $row)
    {
    	$val = $row->getData($this->getColumn()->getIndex());
    	if($val){
	    	$options = explode(';', $val);
	    	foreach ($options as $key => $value) {
	    		$options_new[] = explode(':',$value);
	    	}
	    	$html = '<ul>';
	    	foreach ($options_new as $key => $value) {
	    		if(count($value) > 1){
	    			$html .= '<li ';
			        $html .= 'id="'. $this->getColumn()->getId().'">';
			        $html .= $value[0].' : '.$value[1];
			        $html .= '</li>';
	    		}    
	    	}
	    	$html .= '</ul>';
	    	return $html;
    	}    	
    }
}