<?php


namespace ProVu\OrderStatus\Cron;

class OrderSync
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $username = 'pbxphonesys.apitest';
		$password = 'mBB:q8K~S';
		$this->updateOrder($username, $password);
    }
	
	public function updateOrder($username, $password)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/provu.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$statuses = ['processing','part_shipped'];
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$state = $objectManager->get('Magento\Framework\App\State');

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
						$transactionSave = $_transactionFactory->addObject($shipment)->addObject($shipment->getOrder());
						$transactionSave->save();
						$_shipmentSender_shipmentSender->send($shipment);
						$objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
						$shipment->save();
						
					} catch (\Exception $e) {
						$logger->info($e->getMessage());
					}

				}
				if ($orderData['Status'] == 'Shipped' || $orderData['Status'] == 'Delivered') {
					
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
						$logger->info($incrementId.":Shipment Created");
					} catch (\Exception $e) {
						$logger->info($e->getMessage());
					}
				}
			} else {

			}
		}
		$logger->info("Provu Order status running...");
	}
}
