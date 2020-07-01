<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model;

use Magento\Framework\App\Action\Context;
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;

/**
 * custom cron actions
 */
class Cron
{
    /**
     * @var ProductmapRepositoryInterface
     */
    protected $_eBayProductMap;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Logger\Logger
     */
    protected $_ebayLogger;

    /**
     * @var Magento\Framework\App\Action\Context
     */
    protected $_contextController;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param Context                                                     $context
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData      $manageDataHelper
     * @param \Webkul\MultiEbayStoreMageConnect\Logger\Logger             $ebayLogger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                 $date
     * @param \Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory $ebayAccount
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data               $helper
     * @param ProductmapRepositoryInterface                               $productMapRepository
     */
    public function __construct(
        Context $context,
        \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageDataHelper,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $ebayLogger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory $ebayAccount,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        ProductmapRepositoryInterface $productMapRepository
    ) {
        $this->_manageDataHelper = $manageDataHelper;
        $this->_ebayLogger = $ebayLogger;
        $this->_contextController = $context;
        $this->date = $date;
        $this->_ebayAccount = $ebayAccount;
        $this->_helper = $helper;
        $this->_productMapRepository = $productMapRepository;
    }

    public function OrderSyncFromEbay()
    {
        $helper = $this->_helper;
        $allStoreDetails = $this->_ebayAccount->create()->getCollection();
        foreach ($allStoreDetails as $store) {
            $storeId = $store->getEntityId();
            $this->_ebayLogger->info('Current Seller Id '.$storeId);
            $eBayConfig = $helper->getEbayAPI($storeId);
            $path = 'multiebaystoremageconnect/import_status/order_import_enable';
            $importEnable = $this->_helper->getConfigValue($path);
            if ($eBayConfig && $importEnable || $helper->getSoldItemAction() == 'inventory_management') {
                $dt = new \DateTime();
                $currentDate = $dt->format('Y-m-d\TH:i:s');
                // $dt->modify('-30 day');
                $dt->modify('-1 hours');
                $endTime = $dt->format('Y-m-d\TH:i:s');

                $this->_ebayLogger->info("Current date and time");
                $this->_ebayLogger->info($currentDate);
                $pageNumber = 0;
                $pagenumber = $pageNumber ? $pageNumber + 1 : 1;
                                /****/
                $params = [
                    'Version' => 891,
                    'DetailLevel' => 'ReturnAll',
                    'Pagination' => [
                        'EntriesPerPage' => '100',
                        'PageNumber' => ($pagenumber ? $pagenumber : 1)
                    ],
                    'CreateTimeFrom' => $endTime,
                    'CreateTimeTo' => $currentDate,
                    'OrderStatus' => 'Completed',
                ];

                $request = $this->_contextController->getRequest();
                $results = $eBayConfig->GetOrders($params);
                if (isset($results->OrderArray->Order)) {
                    $eBayOrders = json_decode(json_encode($results->OrderArray->Order), true);
                    $eBayOrders = isset($eBayOrders[0]) ? $eBayOrders : [0 => $eBayOrders];
                    $productCount = 0;
                    foreach ($eBayOrders as $eBayOrder) {
                        $itemId = $eBayOrder['TransactionArray']['Transaction']['Item']['ItemID'];
                        $quantityPurchased = $eBayOrder['TransactionArray']['Transaction']['QuantityPurchased'];
                        $syncProMap = $this->_productMapRepository->getRecordByEbayProductId($itemId)->getFirstItem();
                        /*if (!$syncProMap->getEntityId()) {
                            $params = [
                                'Version' => 849, //version
                                'ItemID' => $itemId,
                                'DetailLevel'=> 'ReturnAll'
                            ];

                            $resultsProduct = $eBayConfig->GetItem($params);

                            $resultsProArray[] = $resultsProduct->Item;
                            //$this->_manageDataHelper->ManageProductRawData($resultsProArray, $storeId, $request, true);
                            $this->_ebayLogger->info(" productCount : ".$productCount);
                            $productCount++;
                        }*/
                        $this->_helper->updateItemQtyAtMage($storeId, $eBayConfig, $itemId);
                    }
                    $this->_ebayLogger->info(" new product created count ".$productCount);
                    $this->_ebayLogger->info(" order creation start");
                    //$items = $this->_manageDataHelper->ManageOrderRawData($eBayOrders, $storeId, true);
                    $this->_ebayLogger->info(" completed order creation process");
                    //$this->_ebayLogger->info(" order data ".json_encode($items));
                }
            } else {
                $this->_ebayLogger->info('InCorrect ebay config of seller id '.$storeId.'. Or order impor disabled by admin.');
            }
        }
    }
}
