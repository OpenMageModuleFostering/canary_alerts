<?php
class Canary_Alerts_Block_Adminhtml_Form_Store_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	$form = new Varien_Data_Form(array(
  			'id' => 'edit_form',
  			'action' => $this->getUrl('*/*/savestore'),
  			'method' => 'post',
  	));
  	$form->setUseContainer(true);
  	$this->setForm($form);
  	$fieldset = $form->addFieldset('form_general', array('legend' => Mage::helper('canaryalerts')->__('Please select store.')));
  	$storeDropDownValues = Mage::registry("canary_alerts_stores_dropdown");
  	$fieldset->addField('alerts_store', 'select', array(
  			'label'     => Mage::helper('canaryalerts')->__('Select Store'),
  			'class'     => 'required-entry',
  			'required'  => true,
  			'name'      => 'alerts_store',
  			'values'     => $storeDropDownValues
  	));
  	return parent::_prepareForm();
  }
  
}