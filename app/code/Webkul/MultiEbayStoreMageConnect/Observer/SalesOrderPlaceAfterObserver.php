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
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $ebayLogger,
        ProductmapRepositoryInterface $productMapRepository,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productloader
    ) {
        $this->productloader = $productloader;
        $this->logger = $ebayLogger;
        $this->productMapRepository = $productMapRepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->helper = $helper;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $ebayOrderId = null;
            $order = $observer->getOrder();
            $lastOrderId = $order->getId();
            $orderIncrementedId = $order->getIncrementId();
            $orderItems = $order->getAllVisibleItems();
            foreach ($orderItems as $item) {
                $productIds[$item->getProductId()] = $item->getQtyOrdered();
            }
            $this->logger->info('Order quatity with product ids');
            $this->logger->info(json_encode($productIds));

            foreach ($productIds as $productId => $itemOrderQty) {
                $product = $this->productloader->create()->load($productId);
                $ebayMappedModel = $this->productMapRepository
                                 ->getRecordByMageProductId($productId)->getFirstItem();

                if ($ebayMappedModel->getEntityId()) {
                    //$productQty = $product->getExtensionAttributes()->getStockItem()->getQty();

                    $ebayItemId = $ebayMappedModel->getEbayProId();

                    $helper = $this->helper;
                    $eBayConfig = $helper->getEbayAPI($ebayMappedModel->getRuleId());
                    if ($eBayConfig) {
                        $this->logger->info(' updated product quantity in ebay');

                        $params = [
                            'Version' => 849, //version
                            'ItemID' => $ebayItemId,
                            'DetailLevel'=> 'ReturnAll'
                        ];

                        $resultsProduct = $eBayConfig->GetItem($params);

                        $ebayTotalQty = $resultsProduct->Item->Quantity;
                        $ebaySoldQty = $resultsProduct->Item->SellingStatus->QuantitySold;
                        $actualQty = $ebayTotalQty - ($ebaySoldQty + $itemOrderQty);


                        $ebayParams = [
                            'Version' => 891,
                            'Item' => [
                                "ItemID" => $ebayItemId,
                                'Quantity' => $actualQty,
                            ],
                            'WarningLevel' => 'High'
                        ];

                        if ($ebayMappedModel->getProductType() === "configurable") {
                            $variations = $helper->getProductVariationForEbay($product, []);
                            $ebayParams['Item']['Variations'] = $variations;
                            unset($ebayParams['Item']['StartPrice']);
                            unset($ebayParams['Item']['Quantity']);
                            $response = $eBayConfig->ReviseFixedPriceItem($ebayParams);
                        } else {
                            $response = $eBayConfig->ReviseItem($ebayParams);
                        }

                        $this->logger->info('ebay response regarding quantity updation');
                        $this->logger->info(json_encode($response));
                    } else {
                        $this->logger
                        ->info('ebay details not correct '.$ebayMappedModel->getRuleId());
                    }
                } else {
                    $this->logger->info('this product id '.$productId .' is not an ebay product');
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Observer SalesOrderPlaceAfterObserver : '.$e->getMessage());
        }
    }
}
