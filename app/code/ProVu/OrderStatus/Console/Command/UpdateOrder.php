<?php

namespace ProVu\OrderStatus\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Sales\Model\Order;

class UpdateOrder extends Command
{
	protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $username = 'pbxphonesys.apitest';
		$password = 'mBB:q8K~S';
		//$this->updateOrder($username, $password);
		//$this->uploadOrder($username, $password);
		$this->removeSmallImage();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("provu:order");
        $this->setDescription("Imports and Updates from Provu");
        parent::configure();
    }
	
	public function removeSmallImage() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
		//$_productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')->addFieldToFilter('sku',array("eq" => 'CP920'));
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		$imageProcessor = $objectManager->create('\Magento\Catalog\Model\Product\Gallery\Processor');
		foreach ($_productCollection as $_product) {
			$_product = $objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());
			$productimages = $_product->getMediaGalleryImages();
			foreach($productimages as $productimage)
			{
				if (strpos($productimage['path'], 'thumb') !== false) {
					$imageProcessor->removeImage($_product, $productimage->getFile());
				}
				if (strpos($productimage['path'], 'large') !== false) {
					$imageProcessor->removeImage($_product, $productimage->getFile());
					$newPath = $productimage['path'];
					if (file_exists($newPath)) {
						$imageProcessor->addImage($_product, $newPath, array('image','thumbnail','small_image'), false, false);
						$_product->save();
						echo $_product->getSku().": Thumbnail image removed.".PHP_EOL;
					} else {
						echo "Not exists".PHP_EOL;
					}
					continue;
				}
			}
			echo $_product->getSku().PHP_EOL;
		}
		
	}
	
	public function uploadOrder($username, $password)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$orderIds = array(12,13);
		$_orderCollectionFactory = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
		$_orderCollection = $_orderCollectionFactory->create()
			->addFieldToSelect('*')
			->addFieldToFilter('entity_id',
                ['in' => $orderIds]
            );
		foreach($_orderCollection as $_order) {
			$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($_order->getId());
			$poNumber = $order->getPoNumber();
			$address = $order->getBillingAddress();
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
					$qty = intval($orderItem->getQtyOrdered());
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
			echo $order->getIncrementId()." was uploaded to Provu".PHP_EOL;
			curl_close($ch);
		}
	}
	
	public function updateOrder($username, $password)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$statuses = ['processing','part_shipped'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->setAreaCode('adminhtml');
		$_orderCollectionFactory = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
		$_trackFactory = $objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory');
		$_transactionFactory = $objectManager->create('Magento\Framework\DB\Transaction');
		$_shipmentSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\ShipmentSender');
		$_orderCollection = $_orderCollectionFactory->create()
			->addFieldToSelect('*')
			->addFieldToFilter('status',
                ['in' => $statuses]
            );

		$context = stream_context_create(array(
				'http' => array(
					'header'  => "Authorization: Basic " . base64_encode("$username:$password")
				)
			)
		);
		foreach($_orderCollection as $_order) {
			$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($_order->getId());
			$incrementId = $order->getIncrementId();
			$server = 'https://secure.provu.co.uk/prosys/order_status.php?cusRefNum='.$incrementId.'&xml=yes';
			$data = file_get_contents($server, false, $context);
			$xml = simplexml_load_string($data);
			$json  = json_encode($xml);
			$orderData = json_decode($json, true);
			if (isset($orderData['ordid'])) {
				if ($orderData['Status'] == 'Cancelled') {
					$order->setStatus('canceled');
					$order->save();
				}
				if ($orderData['Status'] == 'Part Shipped') {
					
					if ($order->getStatus() == 'part_shipped') {
						continue;
					} 
					
					//If there is no invoice then create
					/*$_invoiceService = $objectManager->create('Magento\Sales\Model\Service\InvoiceService');
					$invoice = $_invoiceService->prepareInvoice($order);
					$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
					$invoice->register();
					$invoice->getOrder()->setCustomerNoteNotify(false);
					$invoice->getOrder()->setIsInProcess(true);
					$order->addStatusHistoryComment(__('Automatically INVOICED'), false);
					$transactionSave = $_transactionFactory->addObject($invoice)->addObject($invoice->getOrder());
					$transactionSave->save();*/
					
					//Get Order Item Details
					$provuItems = array();
					if (count($orderData['Orderlines']) > 0) {
						$orderLines = $orderData['Orderlines'];
						foreach ($orderLines as $orderLine) {
							$provuItems[$orderLine['item']] = intval($orderLine['quantity']) - intval($orderLine['quantity_outstand']);
						}
					}
					
					$convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
					$shipment = $convertOrder->toShipment($order);
					foreach ($order->getAllItems() as $orderItem) {
						$qtyShipped = $provuItems[$orderItem->getSku()];
						$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
						$shipment->addItem($shipmentItem);
					}
					
					$state = $objectManager->get('Magento\Framework\App\State');
					$state->setAreaCode('adminhtml');
					$shipment->register();
					$shipment->setCustomerNoteNotify(true);
					$shipment->getOrder()->setIsInProcess(true);

					try {
						//Check Tracking Number
						if (count($orderData['deliveries']) > 0) {
							$despatch = $orderData['deliveries']['despatch'];
							$trackNo = $despatch['ptp_consignment'];

							$data = array(
								'carrier_code' => 'apc',
								'title' => $order->getShippingDescription(),
								'number' => $trackNo,
							);
							$track = $_trackFactory->create()->addData($data);
							$shipment->addTrack($track)->save();
							
						}
						$order->setStatus('part_shipped');
						$order->save();
						//$shipment->getOrder()->save();
						$transactionSave = $_transactionFactory->addObject($shipment)->addObject($shipment->getOrder());
						$transactionSave->save();
						$_shipmentSender_shipmentSender->send($shipment);
						$objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
						$shipment->save();
						
					} catch (\Exception $e) {
						$logger->info($e->getMessage());
					}
					echo $incrementId.":".$orderData['Status'].PHP_EOL;
				}
				if ($orderData['Status'] == 'Shipped' || $orderData['Status'] == 'Delivered') {
					//If there is no invoice then create
					/*$_invoiceService = $objectManager->create('Magento\Sales\Model\Service\InvoiceService');
					$invoice = $_invoiceService->prepareInvoice($order);
					$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
					$invoice->register();
					$invoice->getOrder()->setCustomerNoteNotify(false);
					$invoice->getOrder()->setIsInProcess(true);
					$order->addStatusHistoryComment(__('Automatically INVOICED'), false);
					$transactionSave = $_transactionFactory->addObject($invoice)->addObject($invoice->getOrder());
					$transactionSave->save();*/
					
					//Create Shipment
					$convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
					$shipment = $convertOrder->toShipment($order);
					foreach ($order->getAllItems() as $orderItem) {
						$qtyShipped = $orderItem->getQtyOrdered();
						$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
						$shipment->addItem($shipmentItem);
					}

					$shipment->register();
					$shipment->setCustomerNoteNotify(true);
					$shipment->getOrder()->setIsInProcess(true);

					try {
						//Check Tracking Number
						if (count($orderData['deliveries']) > 0) {
							$despatch = $orderData['deliveries']['despatch'];
							$trackNo = $despatch['ptp_consignment'];

							$data = array(
								'carrier_code' => 'apc',
								'title' => $order->getShippingDescription(),
								'number' => $trackNo,
							);
							$track = $_trackFactory->create()->addData($data);
							$shipment->addTrack($track)->save();
							
						}

						$shipment->getOrder()->save();
						$objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
						$transactionSave = $_transactionFactory->addObject($shipment)->addObject($shipment->getOrder());
						$transactionSave->save();
						$_shipmentSender->send($shipment);
						$shipment->save();
					} catch (\Exception $e) {
						$logger->info($e->getMessage());
					}

					echo $incrementId.":".$orderData['Status'].PHP_EOL;
				}
			} else {
				echo $incrementId.":Not Exists at Provu".PHP_EOL;
			}
		}
	}
}