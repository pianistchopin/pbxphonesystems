<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;

class Index extends Action
{

    protected $_eBayProductMap;

    /**
     * @var \Webkul\Ebaymagentoconnect\Logger\Logger
     */
    protected $_ebayLogger;

    /**
     * @var Magento\Framework\App\Action\Context
     */
    protected $_contextController;

    /**
     * [$date description]
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @param Context                                         $context
     * @param \Webkul\Ebaymagentoconnect\Helper\ManageRawData $manageDataHelper
     * @param \Webkul\Ebaymagentoconnect\Model\Productmap     $productmap
     * @param \Webkul\Ebaymagentoconnect\Logger\Logger        $ebayLogger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime     $date
     */
    public function __construct(
        Context $context,
        \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageDataHelper,
        \Webkul\MultiEbayStoreMageConnect\Model\Productmap $productmap,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $ebayLogger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\MultiEbayStoreMageConnect\Model\EbayaccountsFactory $ebayAccount,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        ProductmapRepositoryInterface $productMapRepository
    ) {
        parent::__construct($context);
        $this->_manageDataHelper = $manageDataHelper;
        $this->_eBayProductMap = $productmap;
        $this->_ebayLogger = $ebayLogger;
        $this->_contextController = $context;
        $this->date = $date;
        $this->_ebayAccount = $ebayAccount;
        $this->_helper = $helper;
        $this->_productMapRepository = $productMapRepository;
    }


    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $helper = $this->_helper;
            $allStoreDetails = $this->_ebayAccount->create()->getCollection();
                                // ->addFieldToFilter('entity_id',2);
            // echo "allStoreDetails<pre>";
            // print_r($allStoreDetails->getData());die;
            foreach ($allStoreDetails as $store) {
                $storeId = $store->getEntityId();
                $this->_ebayLogger->info('Current Seller Id '.$storeId);
                $eBayConfig = $helper->getEbayAPI($storeId);
                if ($eBayConfig) {
                    $dt = new \DateTime();
                    $currentDate = $dt->format('Y-m-d\TH:i:s');
                    $dt->modify('-2 day');
                    // $dt->modify('-10 hours');
                    $endTime = $dt->format('Y-m-d\TH:i:s');
    
                    $this->_ebayLogger->info(" Current date and time ");
                    $this->_ebayLogger->info($currentDate);
                    $pageNumber = 0;
                    $pagenumber = $pageNumber ? $pageNumber + 1 : 1;
                                    /****/
                    $params = ['Version' => 891,
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
                    // echo "e<pre>";
                    // print_r($results);die;
                    if (isset($results->OrderArray->Order)) {
                        $eBayOrders = json_decode(
                            json_encode($results->OrderArray->Order),
                            true
                        );
                        $eBayOrders = isset($eBayOrders[0]) ?
                                        $eBayOrders : [0 => $eBayOrders];
    
                        $productCount = 0;
                        foreach ($eBayOrders as $eBayOrder) {
                            $itemId = $eBayOrder['TransactionArray']['Transaction']['Item']['ItemID'];
    
                            $syncProMap = $this->_productMapRepository
                                 ->getRecordByEbayProductId($itemId)->getFirstItem();
                            if (!$syncProMap->getEntityId()) {
                                $params = [
                                    'Version' => 849, //version
                                    'ItemID' => $itemId,
                                    'DetailLevel'=> 'ReturnAll'
                                ];
    
                                $resultsProduct = $eBayConfig->GetItem($params);
    
                                
                                $resultsProArray[] = $resultsProduct->Item;
                                $this->_manageDataHelper->ManageProductRawData($resultsProArray, $storeId, $request, true);
                                $this->_ebayLogger->info(" productCount : ".$productCount);
                                $productCount++;
                            }
                        }
                        $this->_ebayLogger->info(" new product created count ".$productCount);
                        $this->_ebayLogger->info(" order creation start");
                        $items = $this->_manageDataHelper->ManageOrderRawData($eBayOrders, $storeId, true);
                        $this->_ebayLogger->info(" completed order creation process");
                        $this->_ebayLogger->info(" order data ".json_encode($items));
                    } else {
                        $this->_ebayLogger->info('InCorrect ebay config of seller id'.$storeId);
                    }
                } else {
                    $this->_ebayLogger->info('InCorrect ebay config of seller id'.$storeId);
                }
            }
        } catch (\Exception $e) {
            $this->_ebayLogger->info('Cron contolrer : '.$e->getMessage());
        }
    }
}
