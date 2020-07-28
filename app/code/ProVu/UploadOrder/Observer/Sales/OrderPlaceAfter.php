<?php

namespace ProVu\UploadOrder\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class OrderPlaceAfter implements ObserverInterface
{
    protected $orderFactory;

    public function __construct(\Magento\Quote\Model\QuoteFactory $quoteFactory,
    \Magento\Sales\Model\Order $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if(isset($_SESSION['ponumber'])){
            $poNumber = $_SESSION['ponumber'];
        }
        else{
            $poNumber = 0;
        }


		$order->setPoNumber($poNumber);
		$this->uploadOrderToProvu($order, $poNumber);

    }
	
	public function uploadOrderToProvu($order, $poNumber) 
	{
		$username = 'pbxphonesys.apitest';
		$password = 'mBB:q8K~S';
		$address = $order->getBillingAddress();
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Order Uploaded Started.');
		$poststring = '';
		$poststring = "<order_conf>\n";
		$orderNum = $order->getIncrementId();
		$poststring .= "<OrderRef>$orderNum</OrderRef>\n"; 
		$poststring .= "<cus_cusRefNum>$poNumber</cus_cusRefNum>\n"; 
		if ($address) {
			$name = $address->getFirstname().' '.$address->getLastname();
			$poststring .= "<Name>$name</Name>\n";
			$cname = $address->getCompany();
			$poststring .= "<CName>$cname</CName>\n";
			$street = $address->getStreet();
			$street = is_array($street) ? implode("\n", $street) : $street;
			if ($address->getRegion()) {
				$street = $street . ' ' . $address->getRegion();
			}
			if ($address->getCity()) {
				$street = $street . ' ' . $address->getCity();
			}
			$poststring .= "<Address>$street</Address>\n";
			$postcode = $address->getPostcode();
			$poststring .= "<Postcode>$postcode</Postcode>\n"; 
			$country = $address->getCountryId();
			$poststring .= "<Country>$country</Country>\n"; 
			$phone = $address->getTelephone();
			$poststring .= "<Phone>$phone</Phone>\n"; 
			$email = $order->getCustomerEmail();
			$poststring .= "<Email>$email</Email>\n"; 
			$orderItems = $order->getAllItems();
			$poststring .= "<lines>\n"; 
			foreach ($orderItems as $orderItem) {
				$poststring .= "<line>\n"; 
				$sku = $orderItem->getSku();
				$poststring .= "<Item>$sku</Item>\n"; 
				$qty = $orderItem->getQtyOrdered();
				$poststring .= "<Quantity>$qty</Quantity>\n"; 
				$poststring .= "</line>\n";
			}
			$poststring .= "</lines>\n";
		}
		$poststring .= "</order_conf>\n";
		$posturl = "https://secure.provu.co.uk/prosys/xml.php";
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $posturl); 
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring); 
		$result = curl_exec($ch);
		curl_close($ch);
		$logger->info('Order '.$poNumber.' has been uploaded.');
	}
}
