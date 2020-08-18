<?php

namespace ProVu\UploadOrder\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class OrderPlaceAfter implements ObserverInterface
{
    protected $orderFactory;
    protected $productRepositoryInterface;
    protected $product;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order $orderFactory,
        ProductRepositoryInterface $productRepositoryInterface,
        Product $product
)
    {
        $this->orderFactory = $orderFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->product =$product;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if(!isset($_SESSION['ponumber'])){
            $poNumber = 0;
        }
        else{
            $poNumber = $_SESSION['ponumber'];
        }
        $order->setPoNumber($poNumber);
        $this->uploadOrderToProvu($order, $poNumber);
    }
	
	public function uploadOrderToProvu($order, $poNumber) 
	{
        $provu_flag = false;
        $ebay_sku_arr = array();
        $provu_sku_arr = array();

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
                $sku = $orderItem->getSku();
                if($this->checkSkuFromProduct($sku)) {
                    $poststring .= "<line>\n";
                    $poststring .= "<Item>$sku</Item>\n";
                    $qty = $orderItem->getQtyOrdered();
                    $poststring .= "<Quantity>$qty</Quantity>\n";
                    $poststring .= "</line>\n";
                    $provu_flag = true;

                    array_push($provu_sku_arr, $sku);
                }
                else{
                    array_push($ebay_sku_arr, $sku);
                }
			}
			$poststring .= "</lines>\n";
		}
		$poststring .= "</order_conf>\n";
		$posturl = "https://secure.provu.co.uk/prosys/xml.php";


		if($provu_flag){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $posturl);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
            $result = curl_exec($ch);
            curl_close($ch);

            $provu_sku_str = '';
            foreach($provu_sku_arr as $provu_sku){
                $provu_sku_str .= $provu_sku.', ';
            }
            $logger->info('Order '.$poNumber.'(sku: '.$provu_sku_str.') has been uploaded to provu.');
        }

        $ebay_sku_str = '';
        foreach($ebay_sku_arr as $ebay_sku){
            $ebay_sku_str .= $ebay_sku.', ';
        }

        unset($_SESSION["ponumber"]);
	}

	public function checkSkuFromProduct($sku){
        $provu_flag = false;
        $product = $this->product->getIdBySku($sku);
        if($product){
            $product_interface = $this->productRepositoryInterface->get($sku);
            $provu_update_time = $product_interface->getData('provu_updated_time');
            if($provu_update_time != '' ){
                $provu_flag = true;
            }
        }
        return $provu_flag;
    }
}
