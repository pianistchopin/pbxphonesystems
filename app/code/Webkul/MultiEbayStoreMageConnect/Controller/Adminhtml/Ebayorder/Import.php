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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Ebayorder;

class Import extends Ebayorder
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData
     */
    protected $_manageDataHelper;

    /**
     * @param Context                                                $context
     * @param JsonFactory                                            $resultJsonFactory
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageDataHelper
     * @param \Webkul\MultiEbayStoreMageConnect\Helper\Data          $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Webkul\MultiEbayStoreMageConnect\Helper\ManageRawData $manageDataHelper,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_manageDataHelper = $manageDataHelper;
        $this->_helper = $helper;
    }

    /**
     * eBay order import controller.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $notifications = [];
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('MultiEbayStoreMageConnect/*/');

            return;
        }
        try {
            /******/
            if ($this->getRequest()->isPost()) {
                $items = [];
                $id = $this->getRequest()->getParam('id');
                $pagenumber = $this->getRequest()->getParam('page');
                $helper = $this->_helper;
                $eBayClient = $helper->getEbayAPI($id);
                $path = 'multiebaystoremageconnect/import_status/order_import_enable';
                $importEnable = $this->_helper->getConfigValue($path);
                if ($eBayClient && $importEnable) {
                    $resultJson = $this->_resultJsonFactory->create();

                    $dt = new \DateTime();
                    $currentDate = $dt->format('Y-m-d\TH:i:s');
                    $dt->modify('-90 day');
                    $endTime = $dt->format('Y-m-d\TH:i:s');

                    $pageNumber = 0;
                    //$items=array();
                    $responce = [];
                    $errorMsg = '<br/>';
                    $tmpWholeData = [];
                    // import data from ebay
                    do {
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

                        $count = 0;
                        $results = $eBayClient->GetOrders($params);
                        $i = 0;
                        if (isset($results->OrderArray->Order)) {
                            $eBayOrders = json_decode(
                                json_encode($results->OrderArray->Order),
                                true
                            );
                            $eBayOrders = isset($eBayOrders[0]) ?
                                            $eBayOrders : [0 => $eBayOrders];

                            $notifications = $this->_manageDataHelper->ManageOrderRawData($eBayOrders, $id);

                            $pageNumber = (int) $results->PageNumber;
                            $responce = [
                                'data' => $notifications['items'],
                                'error_msg' => false,
                                'notification' => $notifications['errorMsg']
                            ];
                        } else {
                            if (isset($results->Ack)
                                && $results->Ack != 'Success') {
                                $responce = [
                                    'data' => $notifications['items'],
                                    'error_msg' => $results->Errors->LongMessage
                                ];
                                break;
                            } else {
                                $responce = [
                                    'data' => '',
                                    'error_msg' => false,
                                    'notification' => ''
                                ];
                            }
                        }
                    } while ($results->ReturnedOrderCountActual == 100);
                } else {
                    $responce = [
                        'data' => '',
                        'error_msg' => 'eBay configuration details not set or order import disabled by admin'
                    ];
                }
            } else {
                $responce = ['data' => $notifications['items'],'error_msg' => 'invalid request'];
            }
        } catch (\Exception $e) {
            $responce = ['data' => '', 'error_msg' => $e->getMessage()];
        }
        return $this->_resultJsonFactory->create()->setData($responce);
    }
}
