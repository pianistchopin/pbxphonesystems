<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

class Import extends Categories
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Webkul\Ebaymagentoconnect\Model\EbaycategoryFactory
     */
    protected $_ebaycategoryFactory;

    /**
     * @param Context     $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        \Webkul\MultiEbayStoreMageConnect\Model\EbaycategoryFactory $ebaycategoryFactory
    ) {
    
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helper = $helper;
        $this->_ebaycategoryFactory = $ebaycategoryFactory;
    }

    /**
     * eBay Category Sync Controller.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $helper = $this->_helper;
            $resultJson = $this->_resultJsonFactory->create();
            $eBayConfig = $helper->geteBayConfiguration();
            $client = $helper->getEbayAPI();
            if ($client) {
                $params = [
                            'Version' => 853,
                            'SiteID' => 0,
                            'CategorySiteID' => $eBayConfig['globalsites'],
                            'LevelLimit' => 5,
                            'ViewAllNodes' => true,
                            'DetailLevel' => 'ReturnAll',
                        ];
                $results = $client->GetCategories($params);
                
                $imporCount = 0;
                $storedEbayCates = $this->_ebaycategoryFactory
                            ->create()
                            ->getCollection()->getColumnValues('ebay_cat_id');
                if (isset($results->CategoryArray->Category)) {
                    $ebayAllCategories = [];
                    foreach ($results->CategoryArray->Category as $value) {
                        if (!in_array($value->CategoryID, $storedEbayCates)) {
                            $ebayAllCategories[] = [
                                'ebay_cat_id'       => $value->CategoryID,
                                'ebay_cat_parentid' => $value->CategoryParentID,
                                'ebay_cat_name'     => $value->CategoryName
                            ];
                            ++$imporCount;
                        }
                    }
                    if ($imporCount) {
                        $this->_helper->InsertDataInBulk($ebayAllCategories);
                        $msg = $imporCount.' eBay Categories imported successfully';
                    } else {
                        $msg = 'No new ebay categories found';
                    }
                    
                    $result['success'] = __($msg);
                } else {
                    if (count($results->detail)) {
                        $result['msg'] = $results
                                        ->detail
                                        ->FaultDetail->DetailedMessage;
                    }
                }
            } else {
                $msg = 'Please fill all ebay details in configuration';
                $result['msg'] = __($msg);
            }

            return $resultJson->setData($result);
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();

            return $resultJson->setData($result);
        }
    }
}
