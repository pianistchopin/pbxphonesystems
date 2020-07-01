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

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Productmap
     */
    private $productMapRecord;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItemRepository;

    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $ebayLogger,
        ProductmapRepositoryInterface $productMapRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRepository,
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->logger = $ebayLogger;
        $this->productMapRepository = $productMapRepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->messageManager = $managerInterface;
        $this->helper = $helper;
        $this->backendSession = $backendSession;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if (!$this->backendSession->getEbaySession()) {
                $_product = $observer->getProduct();
                $ebayMappedModel = $this->productMapRepository
                                        ->getRecordByMageProductId($_product->getId())->getFirstItem();
                if ($ebayMappedModel->getEntityId()) {
                    $eBayDefaultSetting = $this->helper->getEbayDefaultSettings($ebayMappedModel->getRuleId());
                    $client = $this->helper->getEbayAPI($ebayMappedModel->getRuleId());

                    $productStock = $this->stockItemRepository->getStockItem(
                        $_product->getId(),
                        $_product->getStore()->getWebsiteId()
                    );
                    $itemReviseStatus = $this->helper->getItemReviseStatus();
                    $qtyUpdate = $this->helper->getSoldItemAction();
                    if ($itemReviseStatus && 0) {
                        $ebayParams = [
                            'Version' => 891,
                            'Item' => [
                                "ItemID" => $ebayMappedModel->getEbayProId(),
                                "Title" => $_product->getName(),
                                'StartPrice' => $_product->getPrice(),
                                'SubTitle' => $_product->getName(),
                                'Description' =>$_product->getDescription(),
                                //'Currency' => $eBayDefaultSetting['Currency'],
                                'Quantity' => $productStock->getQty(),
                                'ShippingPackageDetails' => [
                                    'MeasurementUnit' => 'English',
                                    'WeightMajor' => $_product->getWeight(),
                                    'WeightMinor' => $_product->getWeight()-1
                                ],
                            ],
                            'WarningLevel' => 'High'
                        ];

                        if ($ebayMappedModel->getProductType() === "configurable") {
                            $variations = $this->helper->getProductVariationForEbay($_product, []);
                            $ebayParams['Item']['Variations'] = $variations;
                            unset($ebayParams['Item']['StartPrice']);
                            unset($ebayParams['Item']['Quantity']);
                            $response = $client->ReviseFixedPriceItem($ebayParams);
                        } else {
                            $response = $client->ReviseItem($ebayParams);
                        }
                    } else if($qtyUpdate) {
                        $ebayParams = [
                            'Version' => 891,
                            'InventoryStatus' => ["ItemID" => $ebayMappedModel->getEbayProId(), "Quantity" => $productStock->getQty()],
                            'WarningLevel' => 'High'
                        ];
                        $response = $client->ReviseInventoryStatus($ebayParams);
                    }
                    if (isset($response->Ack) && ((string) $response->Ack == 'Success'
                        || (string) $response->Ack == 'Warning')) {
                        $infoMsg = 'Magento product id '.$_product->getId().'revised at eBay Item Id '
                                                            .$ebayMappedModel->getEbayProId();
                        $this->logger->info($infoMsg);
                    } else {
                        $this->manageError($response, $_product->getSku());
                    }
                    $this->logger->info('Observer CatalogProductSaveAfter : ebay update reponse');
                    $this->logger->info(json_encode($response));
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Observer CatalogProductSaveAfter'.$e->getMessage());
        }
    }

    /**
     * manageError
     * @param Object $response
     * @param string $proSku
     * @return void
     */
    private function manageError($response, $proSku)
    {
        try {
            $this->logger->addError(json_encode($response));
            $infoMsg = 'issue return for eBay end so product did not update on eBay end.';
            if (isset($response->Errors) && is_object($response->Errors)) {
                $infoMsg = $response->Errors->ShortMessage. ' (SKU - '.$proSku.')';
                $this->messageManager->addNotice($infoMsg);
            } else {
                if (isset($response->Errors)) {
                    foreach ($response->Errors as $error) {
                        $infoMsg = $error->ShortMessage.' (SKU - '.$proSku.')';
                        $this->messageManager->addNotice($infoMsg);
                    }
                } else {
                    $infoMsg = isset($response->detail) ? $response->faultstring. __(' eBay store error code :')
                                        .$response->detail->FaultDetail->ErrorCode : $response->faultstring;
                    $this->messageManager->addNotice($infoMsg);
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('manageError : '.$e->getMessage());
            $this->messageManager->addNotice($e->getMessage());
        }
    }

}
