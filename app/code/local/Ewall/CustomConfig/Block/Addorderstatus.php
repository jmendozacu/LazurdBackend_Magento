<?php



class Ewall_CustomConfig_Block_Addorderstatus extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoOptions;
    public function __construct()
    {
        // create columns
        $this->addColumn('orderCode', array(
            'label' => Mage::helper('adminhtml')->__('Order Status'),
            'size' => 28,
        ));           

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Line');
        parent::__construct();
        $this->setTemplate('ewall/customconfig/system/config/form/field/array.phtml');
    }

    
    protected function _renderCellTemplate($columnName)
    {    
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        if($columnName === "orderCode"){
            $countryStr = "";
            $OrderStatusArr = Mage::helper('customconfig')->getOrderStatus();          
            foreach($OrderStatusArr as $code => $OrderStatus){

                $OrderStatusStr .= '<option value="'.$code.'">'.$OrderStatus.'</option>';
            }         
            return '<select name="' . $inputName . '">'.$OrderStatusStr.'</select>';

        }else{
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }
    }
}