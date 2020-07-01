<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\MultiEbayStoreMageConnect\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;

class SalesOrderShipmentTrackSaveAfter implements ObserverInterface
{

    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $ebayLogger,
        OrdermapRepositoryInterface $orderMapRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
    ) {
        $this->ebayLogger = $ebayLogger;
        $this->objectManager = $objectManager;
        $this->orderMapRepository = $orderMapRepository;
        $this->helper = $helper;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ebayOrderId = null;
        $event = $observer->getEvent();
        $track = $event->getTrack();
        $shipment = $track->getShipment();
        $order = $shipment->getOrder();
        $trackingId = $track->getNumber();

        $orderIncrementedId = $order->getIncrementId();
       
        
        $trackNumber = $track->getTrackNumber();
        $carrierCode = $track->getCarrierCode();
        $title = $track->getTitle();

        $mappedOrderEbay = $this->orderMapRepository
                        ->getRecordByMageOrderId($orderIncrementedId);

        // echo "mappedOrderEbay<pre>";print_r($mappedOrderEbay->getData());die;
        if ($mappedOrderEbay->getSize()) {
            foreach ($mappedOrderEbay as $ebayOrder) {
                $orderLineItemID = $ebayOrder->getEbayOrderId();
                $ruleId = $ebayOrder->getRuleId();
                break;
            }
            $ebayOrderData = explode('-', $orderLineItemID);

            $ebayOrderId = $ebayOrderData[1];
            $helper = $this->helper;

            $eBayConfig = $helper->getEbayAPI($ruleId);
            if ($eBayConfig) {
                $this->ebayLogger->info(' updated tracking id on ebay order');

                $ebayParams = [
                    'Version' => 891,
                    'ItemID'   => $ebayOrderData[0],
                    'OrderID' => $orderLineItemID,
                    'Shipment' => [
                        "ShipmentTrackingDetails" => [
                            "ShipmentTrackingNumber" => $trackNumber,
                            "ShippingCarrierUsed"    => $carrierCode
                        ]
                    ]
                ];

                $response = $eBayConfig->CompleteSale($ebayParams);

                $this->ebayLogger->info('ebay response regarding saving tracking id');
                $this->ebayLogger->info(json_encode($response));
            } else {
                $this->ebayLogger->info('ebay details not correct ');
            }
        }
    }
}
