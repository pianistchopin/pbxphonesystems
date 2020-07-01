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
use Webkul\MultiEbayStoreMageConnect\Api\ImportedtmpproductRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

class Createproduct extends Products
{
    /**
     * @var ImportedtmpproductRepositoryInterface
     */
    private $importedTmpProductRepository;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Productmap
     */
    private $productMapRecord;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    private $helper;

    /*
    \Magento\Backend\Model\Session
     */
    private $backendSession;

    /**
     * @param Context                                            $context
     * @param ImportedtmpproductRepositoryInterface              $importedTmpProductRepository
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Productmap $productMapRecord
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data      $helper
     */
    public function __construct(
        Context $context,
        ImportedtmpproductRepositoryInterface $importedTmpProductRepository,
        \Webkul\MultiEbayStoreMageConnect\Model\Productmap $productMapRecord,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->importedTmpProductRepository = $importedTmpProductRepository;
        $this->productMapRecord = $productMapRecord;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->backendSession = $context->getSession();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $ruleId = $this->getRequest()->getParam('ruleId');
            $tempData = $this->importedTmpProductRepository
                         ->getCollectionByProductTypeAndRuleId('product', $ruleId)->getFirstItem();
            $helper = $this->helper;
            $helper->ruleId = $ruleId;
            $helper->getEbayAPI($ruleId);
            $this->backendSession->setEbaySession('start');
            $request=$this->getRequest();
            $path = 'multiebaystoremageconnect/import_status/product_import_enable';
            $importEnable = $this->helper->getConfigValue($path);
            if ($tempData->getEntityId() && $importEnable) {
                $tempProData = json_decode($tempData->getProductData(), true);

                $result = $this->processedTempDataToCreatePro($tempProData, $request);

                $data = [
                        'ebay_pro_id' => $tempData->getItemId(),
                        'name' => $tempProData['name'],
                        'price' => $tempProData['price'],
                        'product_type' => $tempProData['type_id'],
                        'rule_id'   => $ruleId
                      ];

                if (isset($result['product_id']) && $result['product_id']) {
                    $data['magento_pro_id'] = $result['product_id'];
                    $data['mage_cat_id'] = $tempProData['category'][0];
                    $record = $this->productMapRecord;
                    $record->setData($data)->save();
                }
                $tempData->delete();
            } elseif(!$importEnable) {
                $result = [
                            'error' => 1,
                            'msg' => __('Product Import disabled by Admin')
                        ];
            } else {
                $data = $this->getRequest()->getParams();
                $total = (int) $data['count'] - (int) $data['skip'];
                $msg = '<div class="wk-mu-success wk-mu-box">'.__('Total ').$total.__(' Product(s) Imported.').'</div>';
                $msg .= '<div class="wk-mu-note wk-mu-box">'.__('Finished Execution.').'</div>';
                $result['msg'] = $msg;
            }
            $this->backendSession->unsEbaySession();
        } catch (\Exception $e) {
            $this->logger->info('Controller Products CreateProduct : '.$e->getMessage());
            $result = [
                        'error' => 1,
                        'msg' => __('Something went wrong, Please check error log.'),
                        'actual_error' => $e->getMessage()
                    ];
        }
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

    /**
     * process temp data to create eBay product
     *
     * @param array $tempProData
     * @return array
     */
    public function processedTempDataToCreatePro($tempProData, $request)
    {
        $result = [];
        if (($tempProData['type_id'] == 'simple') || (isset($tempProData['supperattr']) && count($tempProData['supperattr']) == 0)) {
            if (isset($tempProData['assocate_pro'][0])) {
                $tempProData['price'] = $tempProData['assocate_pro'][0]['price'];
                $tempProData['stock'] = $tempProData['assocate_pro'][0]['qty'];
                $tempProData['type_id'] = 'simple';
                foreach ($tempProData['assocate_pro'][0] as $key => $value) {
                    if (strpos($key, 'conf_') !== false) {
                        $tempProData[$key] = $value;
                    }
                }
                unset($tempProData['assocate_pro']);
                unset($tempProData['supperattr']);
            }
            foreach ($tempProData as $key => $value) {
                $request->setParam($key, $value);
            }
            $result = $this->helper->saveSimpleProduct($request);
        } else {
            foreach ($tempProData as $key => $value) {
                $request->setParam($key, $value);
            }
            $result = $this->helper->saveConfigProduct($request);
        }
        return $result;
    }
}
