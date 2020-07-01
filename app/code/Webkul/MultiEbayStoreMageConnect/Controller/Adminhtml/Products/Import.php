<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

class Import extends Products
{
    public $ruleId;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $scopeConfig;

    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData
     */
    protected $_manageRawDataHelper;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    protected $helper;

    /**
     * @param Context                                                $context
     * @param JsonFactory                                            $resultJsonFactory
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageRawDataHelper
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data          $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageRawDataHelper,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_manageRawDataHelper = $manageRawDataHelper;
        $this->helper = $helper;
    }

    /**
     * Mapped Product List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('multiebaystoremageconnect/*/');

            return;
        }
        try {
            if ($this->getRequest()->isPost()) {
                $this->ruleId = $this->getRequest()->getParam('id');
                $pagenumber = $this->getRequest()->getParam('page');
                $helper = $this->helper;
                $client = $helper->getEbayAPI($this->ruleId);
                $path = 'multiebaystoremageconnect/import_status/product_import_enable';
                $importEnable = $this->helper->getConfigValue($path);
                if ($client && $importEnable) {
                    $eBayConfig = $helper->geteBayConfiguration($this->ruleId);
                    $dt = new \DateTime();
                    $currentDate = $dt->format('Y-m-d\TH:i:s');
                    $dt->modify('+119 day');
                    $endTime = $dt->format('Y-m-d\TH:i:s');
                    $pageNumber = 0;
                    $items = [];
                    $responce = [];
                    $errorMsg = '';
                    $tmpWholeData = [];
                    $importStatus = $this->_getProductImportType($eBayConfig);

                    if (empty($importStatus['import_type'])) {
                        if (empty($importStatus['error_msg'])) {
                            foreach ($importStatus['data'] as $mappedRecord) {
                                $newItems = $this->_callGetSellerList($client, $eBayConfig, $currentDate, $endTime, $mappedRecord['ebay_cat_id']);
                                if (empty($newItems['error_msg']) && !empty($newItems['data'])) {
                                    foreach ($newItems['data'] as $item) {
                                        array_push($items, $item);
                                    }
                                }
                            }
                        } else {
                            $errorMsg = $importStatus['error_msg'];
                            $items = $importStatus['data'];
                        }
                        $responce = ['data' => $items,'error_msg' => $errorMsg];
                    } else {
                        $responce = $this->_callGetSellerList($client, $eBayConfig, $currentDate, $endTime);
                    }
                } else {
                    $responce = [
                        'data' => '',
                        'error_msg' => 'eBay configuration details not set or product import disabled by admin'
                    ];
                }
            } else {
                $responce = ['data' => $items,'error_msg' => 'invalid request'];
            }
        } catch (\Exception $e) {
            $responce = ['data' => $items,'error_msg' => $e->getMessage()];
        }

        return $this->_resultJsonFactory->create()->setData($responce);
    }

    /**
     * get product import type status
     *
     * @return array
     */
    private function _getProductImportType($ebayConfiguration)
    {
        $response = [];
        $error_msg = false;
        $importType = null;
        $data = null;
        if (isset($ebayConfiguration['import_product']) && empty($ebayConfiguration['import_product'])) {
            $mappedCategoryColl = $this->helper->getMappedCategoryData($this->ruleId);
            if (empty($mappedCategoryColl)) {
                $error_msg = __('You haven\'t mapped any ebay category(s).');
            } else {
                $data = $mappedCategoryColl['items'];
            }
            $importType = 0;
        } else {
            $importType = 1;
        }
        return ['data'=>$data, 'error_msg'=>$error_msg, 'import_type'=>$importType];
    }

    /**
     * intract with ebay api
     *
     * @param int $categoryId
     * @return array
     */
    private function _callGetSellerList($client, $sellereBayConfig, $currentDate, $endTime, $categoryId = null)
    {
        $pageNumber = 0;
        $items = [];
        do {
            $pagenumber = $pageNumber ? $pageNumber + 1 : 1;
            $params = [
                'Version' => 849, //version
                'IncludeVariations' => true,
                'UserID' => $sellereBayConfig['ebayuserid'],
                'DetailLevel' => 'ReturnAll',
                'Pagination' => [
                    'EntriesPerPage' => '100',
                    'PageNumber' => ($pagenumber ? $pagenumber : 1)
                ],
                'EndTimeFrom' => $currentDate,
                'EndTimeTo' => $endTime,
            ];

            if (!empty($categoryId)) {
                $params['CategoryID'] = $categoryId;
            }
            $results = $client->GetSellerList($params);

            $i = 0;
            if (isset($results->ItemArray->Item)) {
                $ebayItemObject = $results->ItemArray->Item;
                if (count($results->ItemArray->Item) == 1) {
                    $ebayItemObject = [0 => $results->ItemArray->Item];
                }

                $items = $this->_manageRawDataHelper->ManageProductRawData($ebayItemObject, $this->ruleId);
                $pageNumber = (int) $results->PageNumber;
            } else {
                if (isset($results->Ack) && $results->Ack == 'Success') {
                    $errorMsg = empty($items) ? __('There are no products in your eBay account') : '';
                    $responce = ['data' => $items, 'error_msg' => $errorMsg];
                } elseif (isset($results->detail->FaultDetail->DetailedMessage)) {
                    $errorMsg =  $results->detail->FaultDetail->DetailedMessage;
                    $responce = ['data' => $items, 'error_msg' => $errorMsg];
                } else {
                    $errorMsg = isset($results->Errors->LongMessage) ? $results->Errors->LongMessage :
                                                __("Invalid response from eBay");
                    $responce = ['data' => $items, 'error_msg' => $errorMsg];
                }
                break;
            }
            $responce = ['data' => $items,'error_msg' => false];
        } while ($results->ReturnedItemCountActual == 100);
        return $responce;
    }
}
