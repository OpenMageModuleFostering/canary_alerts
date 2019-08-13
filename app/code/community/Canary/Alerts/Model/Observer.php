<?php
class Canary_Alerts_Model_Observer
{
	private $_ordersApiUrl;
	private $_apiKey;
	public function __construct(){
		$this->_apiKey                    = Mage::getStoreConfig("canaryalerts/api");
		$this->_ordersApiUrl              = "https://canaryalerts.com/api/orders?key=" . $this->_apiKey;
	}
	public function orderPlaced(Varien_Event_Observer $observer){
		if($this->isInstalled()){
			$orderId = $observer['order_ids'][0];
			$feedData = Mage::helper("canaryalerts")->getSaleFeed($orderId);
			$result = Mage::helper("canaryalerts")->sendRequest($this->_ordersApiUrl, $feedData, "POST");
	    	if(!$result){
	    		return false;
	    	}
		}
	}
    public function isInstalled(){
    	$api = Mage::getStoreConfig("canaryalerts/api");
    	return ($api != "false" && $api != "") ? true : false;
    }
   
}
