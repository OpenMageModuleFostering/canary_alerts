<?php
class Canary_Alerts_Block_Adminhtml_Form_Store extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	parent::__construct();
        $this->_blockGroup = 'canaryalerts';
        $this->_controller = 'adminhtml_form';
        $this->_mode = 'store';
       
        $this->_updateButton('save', 'label', Mage::helper('canaryalerts')->__('Update'));
         
    }
    public function getHeaderText()
    {
        return Mage::helper('canaryalerts')->__('Canary Alerts Setup');
    }
}