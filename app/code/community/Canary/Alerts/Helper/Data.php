<?php 
class Canary_Alerts_Helper_Data extends Mage_Core_Helper_Abstract{
	public function _construct(){
		parent::_construct();
	}
	public function sendRequest($url, $data = array(), $method = "GET"){
		Mage::log("Sending request to url:" . $url, null, "canaryalerts.log");
		// Get cURL resource
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

		if($method == "POST"){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS , $data);
		}
		Mage::log($data, null, "canaryalerts.log");
		// Send the request & save response to $resp

		$resp = curl_exec($curl);


		if(!$resp || $resp == "false"){
			return false;
		}
		$header = curl_getinfo ( $curl );
		$httpCode = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		if($httpCode != 200 && $httpCode != 302){
			
		}
		// Close request to clear up some resources
		curl_close($curl);
		return $resp;
	}
	public function getStoreId(){
		return Mage::getStoreConfig("canaryalerts/store_id");
	}
	public function isInstalled(){
		$api = Mage::getStoreConfig("canaryalerts/api");
		return ($api != "false" && $api != "") ? true : false;
	}
	public function getSaleFeed($orderId = false, $store_id = null){
		$data = array();
		$order = Mage::getModel("sales/order")->load($orderId);
		$shippingAddress = $order->getShippingAddress();
		$shippingData    = $shippingAddress->getData();
		$billingAddress  = $order->getBillingAddress();
		$billingData     = $billingAddress->getData();
		$data['billing_address1'] = $billingAddress->getStreet(1);
		$data['billing_address2'] = $billingAddress->getStreet(2);
		$data['billing_city'] = $billingData['city'];
		$data['billing_country'] = Mage::getModel('directory/country')->load($billingData['country_id'])->getName();
		$data['billing_firstname'] = $billingData['firstname'];
		$data['billing_lastname'] = $billingData['lastname'];
		$data['billing_postalcode'] = $billingData['postcode'];
		$data['billing_region'] = $billingData['region'];
		$data['item_count'] = count($order->getItemsCollection());
		$data['shipping_address1'] = $shippingAddress->getStreet(1);
		$data['shipping_address2'] = $shippingAddress->getStreet(2);
		$data['shipping_city'] = $shippingData['city'];
		$data['shipping_country'] = Mage::getModel('directory/country')->load($shippingData['country_id'])->getName();
		$data['shipping_firstname'] = $shippingData['firstname'];
		$data['shipping_lastname'] = $shippingData['lastname'];
		$data['shipping_postalcode'] = $shippingData['postcode'];
		$data['shipping_region'] = $shippingData['region'];
		$data['store_id'] = $store_id ? $store_id : $this->getStoreId();
		$data['subtotal'] = $order->getSubtotal();
		$totals = $order->getTotals();
		if(isset($totals['tax']) && $totals['tax']->getValue()) {
			$tax = $totals['tax']->getValue(); //Tax value if present
		} else {
			$tax = 0;
		}
		$data['taxes'] = $tax;
		$data['total'] = $order->getGrandTotal();
		$data['data_created'] = date(DATE_ATOM, strtotime($order->getCreatedAt()));
		$data['payment_method'] = $order->getPayment()->getMethodInstance()->getTitle();
		$data['shipping_method'] = $order->getShippingDescription();
		$data['reference_id']  = $order->getIncrementId();
		$orderLinesInfo = array();
		foreach ($order->getItemsCollection() as $item){
			$product = Mage::getModel("catalog/product")->load($item['product_id']);
			$itemArray = array();
				
			$regularPrice = number_format($product->getPrice(), 2);
			$discountedPrice = number_format($product->getFinalPrice(), 2);
			$itemArray['discount_price'] = $discountedPrice;
			$itemArray['original_price'] = $regularPrice;
			$itemArray['quantity'] = $item->getData("qty_ordered");
			$itemArray['product_id'] = $product->getId();
			$itemArray['product_name'] = $product->getName();
			$itemArray['product_sku'] = $product->getSku();
			$orderLinesInfo[] = $itemArray;
		}
		$data['details'] = $orderLinesInfo;
		return json_encode($data);
	}
}
?>