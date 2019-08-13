<?php 
class Canary_Alerts_ConfigController extends Mage_Adminhtml_Controller_Action{
	private $_ordersApiUrl;
	private $_apiKey;
	private $_apiUrl = "https://canaryalerts.com/api/";
	private $_configModel;
	public function _construct(){
		parent::_construct();
		$this->_apiKey                    = Mage::getStoreConfig("canaryalerts/api");
		$this->_ordersApiUrl              = "https://canaryalerts.com/api/orders?key=" . $this->_apiKey;
		$this->_configModel               = new Mage_Core_Model_Config();
	}
	public function indexAction(){	
		if(Mage::helper("canaryalerts")->isInstalled()){
			$this->_redirect('*/*/installed');
			return;
		}
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('canaryalerts/adminhtml_form_emailpassword'));
		$this->renderLayout();
	}
	public function getkeyAction(){
		if(Mage::helper("canaryalerts")->isInstalled()){
			$this->_redirect('*/*/installed');
			return;
		}
		if ($data = $this->getRequest()->getPost()) {
			$email = $this->getRequest()->getPost("canary_email");
			$password = $this->getRequest()->getPost("canary_password");
			$url = $this->_apiUrl . "company?email=" . $email . "&password=" . $password;
			$getApiRequest = Mage::helper("canaryalerts")->sendRequest($url, array());
			if($getApiRequest == false){
				$this->_redirect('*/*/index', array( "status" => "failed" ));
				return;
			}
			$key = json_decode($getApiRequest, true)['api_key'];	
			$this->_configModel->saveConfig('canaryalerts/api', $key);
			$dropDownUrl = $this->_apiUrl . "store?key=" . $key;
			$dropDownArray   = json_decode(Mage::helper("canaryalerts")->sendRequest($dropDownUrl, array()));
			$dropDownValues = $this->getDropDownValues($dropDownArray);
			Mage::register("canary_alerts_stores_dropdown", $dropDownValues);
			$this->loadLayout();
			$this->_addContent($this->getLayout()->createBlock('canaryalerts/adminhtml_form_store'));
			$this->renderLayout();
		}
	}
	public function installedAction(){
		$this->loadLayout();

		$api = Mage::getStoreConfig("canaryalerts/api");

		//$message = $this->__("You are all setup to go!") .   " <a href='" . Mage::helper("adminhtml")->getUrl("adminhtml/index/index") . "'>" . $this->__("Click here to return to the admin" ) . "</a>";
		$message = '<iframe style="width: 100%; height: 800px; border: none;" src="https://canaryalerts.com/login?inline=1&key=' . $api . '"></iframe>';
		$this->_addContent($this->getLayout()->createBlock('core/text')->setText($message));
		$this->renderLayout();
	}
	public function savestoreAction(){
		if ($data = $this->getRequest()->getPost()) {
			$store_id = $this->getRequest()->getPost("alerts_store");
		}
		$this->_configModel->saveConfig('canaryalerts/store_id', $store_id);
		$this->sendAllOrders($store_id);
		$this->_redirect('*/*/installed');
	}
	public function sendAllOrders($store_id){
		$orders = Mage::getModel("sales/order")->getCollection()->addFieldToFilter('created_at', array(
   			 'from'     => strtotime('-6 month', time()),
   			 'to'       => time(),
   			 'datetime' => true
		));
		foreach($orders as $order){
			$feedData = Mage::helper("canaryalerts")->getSaleFeed($order->getId(), $store_id);
			$result = Mage::helper("canaryalerts")->sendRequest($this->_ordersApiUrl, $feedData, "POST");
		}
	}
	public function getDropDownValues($dropDownArray){
		$array = array();
		foreach($dropDownArray as $el){
			$array[] = array("value" => $el->id, "label" => $el->name);
		}
		return $array;
	}	
}
?>