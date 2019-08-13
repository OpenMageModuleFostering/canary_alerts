<?php
class Canary_Alerts_Block_Adminhtml_Form_Emailpassword_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	$form = new Varien_Data_Form(array(
  			'id' => 'edit_form',
  			'action' => $this->getUrl('*/*/getkey'),
  			'method' => 'post',
  	));
  	$form->setUseContainer(true);
  	$this->setForm($form);
  	$fieldset = $form->addFieldset('form_general', array('legend' => Mage::helper('canaryalerts')->__('Please type your email and password.')));
  	$fieldset->addField('canary_email', 'text', array(
  			'label'     => Mage::helper('canaryalerts')->__('Canary Email'),
  			'class'     => 'required-entry',
  			'required'  => true,
  			'name'      => 'canary_email'
  	));
  	$fieldset->addField('canary_password', 'password', array(
  			'label'     => Mage::helper('canaryalerts')->__('Canary Password'),
  			'class'     => 'required-entry',
  			'required'  => true,
  			'name'      => 'canary_password'
  	));
	if($status = $this->getRequest()->getParam("status")){
		if($status == "failed"){
			echo "<span style='color:red;'>" . $this->__("We don't have a username / password combination like that on file, please <a href=https://canaryalerts.com/>visit the site</a></span>");
		}
	}
  	return parent::_prepareForm();
  }
  
}