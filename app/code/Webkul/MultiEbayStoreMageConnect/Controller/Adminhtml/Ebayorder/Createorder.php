<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Ebayorder;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Api\ImportedtmpproductRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Ebayorder;

class Createorder extends Ebayorder
{
    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Ordermap
     */
    private $orderMapRecord;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\Data
     */
    private $helperData;

    /**
     * @var OrdermapRepositoryInterface
     */
    private $orderMapRepository;

    /**
     * @var ImportedtmpproductRepositoryInterface
     */
    private $importedTmpProductRepository;

    /**
     * @param Context                                          $context
     * @param \Webkul\MultiEbayStoreMageConnect\Model\Ordermap $orderMapRecord
     * @param OrdermapRepositoryInterface                      $orderMapRepository
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data    $helperData
     * @param ImportedtmpproductRepositoryInterface            $importedTmpProductRepository
     */
    public function __construct(
        Context $context,
        \Webkul\MultiEbayStoreMageConnect\Model\Ordermap $orderMapRecord,
        OrdermapRepositoryInterface $orderMapRepository,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helperData,
        ImportedtmpproductRepositoryInterface $importedTmpProductRepository,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->orderMapRecord = $orderMapRecord;
        $this->helperData = $helperData;
        $this->orderMapRepository = $orderMapRepository;
        $this->importedTmpProductRepository = $importedTmpProductRepository;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        try {
            $ruleId = $this->getRequest()->getParam('ruleId');
            $this->helperData->getEbayAPI($ruleId);
            $tempData = $this->importedTmpProductRepository
                    ->getCollectionByProductTypeAndRuleId('order', $ruleId)
                    ->setPageSize(1)
                    ->getFirstItem();
            if ($tempData->getEntityId()) {
                $tempOrder = json_decode($tempData->getProductData(), true);
                $mapedOrder = $this->orderMapRepository
                            ->getRecordByEbayOrderId($tempOrder['ebay_order_id'])
                            ->setPageSize(1)
                            ->getFirstItem();
                $path = 'multiebaystoremageconnect/import_status/order_import_enable';
                $importEnable = $this->helperData->getConfigValue($path);
                if (!$mapedOrder->getEntityId() && $importEnable) {
                    //Create order in stor as eBay
                    $result = $this->helperData
                                ->createMageOrder($tempOrder);
                    if (isset($result['order_id']) && $result['order_id']) {
                        $data = [
                                'ebay_order_id' => $tempOrder['ebay_order_id'],
                                'mage_order_id' => $result['order_id'],
                                'status' => $tempOrder['order_status'],
                                'rule_id'   => $ruleId
                              ];
                        $record = $this->orderMapRecord;
                        $record->setData($data)->save();
                    }
                } elseif (!$importEnable) {
                    $result = [
                        'error' => 1,
                        'msg' => __('order import disabled by admin.')
                    ];
                } else {
                    $result = [
                        'error' => 1,
                        'msg' => __('eBay order ').$tempOrder['ebay_order_id'].__(' already mapped with store order #').$mapedOrder->getMageOrderId()
                    ];
                }
                $tempData->delete();
            } else {
                $data = $this->getRequest()->getParams();
                $total = (int) $data['count'] - (int) $data['skip'];
                $msg = '<div class="wk-mu-success wk-mu-box">'.__('Total ').$total.__(' Order(s) Imported.').'</div>';
                $msg .= '<div class="wk-mu-note wk-mu-box">'.__('Finished Execution.').'</div>';
                $result['msg'] = $msg;
            }
        } catch (\Exception $e) {
            $this->logger->info('Controller Ebayorder createOrder : '.$e->getMessage());
            $result = [
                'error' => 1,
                'msg' => __('Something went wrong, Please check error log.'),
                'actual_error' => $e->getMessage()
            ];
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
