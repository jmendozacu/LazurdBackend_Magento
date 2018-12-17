<?php
class Ewall_Dirvermanagement_Block_Adminhtml_Driver_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$id = $this->getRequest()->getParam("user_id");
				$this->setForm($form);
				$fieldset = $form->addFieldset("dirvermanagement_form", array("legend"=>Mage::helper("dirvermanagement")->__("Item information")));

						$fieldset->addField("id", "hidden", array(
						"label" => Mage::helper("dirvermanagement")->__("Id"),
						"name" => "id",
						));

						$fieldset->addField("userid", "hidden", array(
						"label" => Mage::helper("dirvermanagement")->__("USER ID"),
						"name" => "userid",
						));

						$fieldset->addField("username", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("User Name "),
						"name" => "username",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("firstname", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("First Name "),
						"name" => "firstname",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("lastname", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Last Name "),
						"name" => "lastname",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Email "),
						"name" => "email",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("mobile", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Mobile Number "),
						"name" => "mobile",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("unique_id", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Mobile Unique Id "),
						"name" => "unique_id",
				        'required'  => true,
				        'class'     => 'required-entry',
						));


						$fieldset->addField("longitude", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Longitude"),
						"name" => "longitude",
				        'required'  => true,
				        'class'     => 'required-entry',
						));

						$fieldset->addField("latitude", "text", array(
						"label" => Mage::helper("dirvermanagement")->__("Latitude "),
						"name" => "latitude",
				        'required'  => true,
				        'class'     => 'required-entry',
						));
						if($id){
							$fieldset->addField("new_password", "password", array(
							"label" => Mage::helper("dirvermanagement")->__("New Password "),
							"name" => "password",
							'id'    => 'new_pass',
							'class' => 'input-text validate-password',

							));
							$fieldset->addField("confirmation", "password", array(
							"label" => Mage::helper("dirvermanagement")->__("Password Confirmation"),
							"name" => "password_confirmation",
							'id'    => 'confirmation',
							'class' => 'input-text validate-cpassword',
							));
						}
						else{
							$fieldset->addField("new_password", "password", array(
							"label" => Mage::helper("dirvermanagement")->__("Password "),
							"name" => "password",
		                    'class' => 'input-text required-entry validate-password',
		                    'required' => true,
							));
							$fieldset->addField("confirm_password", "password", array(
							"label" => Mage::helper("dirvermanagement")->__("Password Confirmation"),
							"name" => "password_confirmation",
		                    'class' => 'input-text required-entry validate-cpassword',
		                    'required' => true,
							));
						}

						 $fieldset->addField('is_active', 'select', array(
						'label'     => Mage::helper('dirvermanagement')->__('This Account Is '),
						'values'   => Ewall_Dirvermanagement_Block_Adminhtml_Driver_Grid::getValueArray7(),
						'name' => 'is_active',
						));


				if (Mage::getSingleton("adminhtml/session")->getDriverData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getDriverData());
					Mage::getSingleton("adminhtml/session")->setDriverData(null);
				}
				elseif(Mage::registry("driver_data")) {
				    $form->setValues(Mage::registry("driver_data")->getData());
				}
				return parent::_prepareForm();
		}
}
