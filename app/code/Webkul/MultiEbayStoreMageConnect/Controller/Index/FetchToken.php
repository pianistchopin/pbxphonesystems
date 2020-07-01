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
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Helper\Data as HelperData;
use Webkul\MultiEbayStoreMageConnect\Model\Ebayaccounts;

class FetchToken extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $_resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $_resultPageFactory,
        JsonFactory $resultJsonFactory,
        HelperData $helperData,
        Ebayaccounts $eBaySellerAccount
    ) {
        $this->_resultPageFactory = $_resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helperData = $helperData;
        $this->eBaySellerAccount = $eBaySellerAccount;
        parent::__construct($context);
    }

    /**
     * MultiEbayStoreMageConnect Detail page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $eBayAuthToken = null;
        $msg = null;
        try {
            //$params = $this->getRequest()->getParams();
            $sellereBayConfig = $this->_helperData->geteBayConfiguration();
            $params = $this->getRequest()->getParams();
            $globalSite = $params['globalSite'];
            $ebayUserId = $params['username'];
            $storeName = $params['storeName'];
            $postalCode = $params['postalCode'];
            $attributeSetId = $params['attributeset_id'];
            $ebaySessionId = $params['sessid'];
            $client = $this->_helperData->getEbayAPI();
            $ruName = $sellereBayConfig['app_ru_name'];
            $newParams = [
                'Version' => 891,
                'SessionID' => $ebaySessionId
            ];
            $results = $client->FetchToken($newParams);

            $error = 1;
            $saved = false;
            if (isset($results->Ack) && $results->Ack === 'Success') {
                $eBayAuthToken = $results->eBayAuthToken;
                $error = 0;
                $mpeBaySeller = $this->eBaySellerAccount->getCollection()
                            ->addFieldToFilter('ebay_user_id', ['eq' => $ebayUserId]);
                if ($mpeBaySeller->getSize()) {
                    foreach ($mpeBaySeller as $eBaySeller) {
                        $eBaySeller->setEbayAuthenticationToken($eBayAuthToken);
                        $eBaySeller->setEntityId($eBaySeller->getEntityId())->save();
                        $saved = true;
                    }
                } else {
                    $mpeBaySellerModel = $this->eBaySellerAccount;
                    $sellerCredentials = [
                        'global_site' => $globalSite,
                        'ebay_user_id' => $ebayUserId,
                        'ebay_authentication_token' => $eBayAuthToken,
                        'ebay_developer_id' => $sellereBayConfig['dev'],
                        'ebay_application_id' => $sellereBayConfig['app'],
                        'ebay_certification_id' => $sellereBayConfig['cert'],
                        'shop_postal_code' => $postalCode,
                        'store_name' => $storeName,
                        'attribute_set_id' => $attributeSetId
                    ];
                    $mpeBaySellerModel->setData($sellerCredentials)->save();
                    $saved = true;
                }
                $msg = __('User has been authenticated successfully.');
                $this->messageManager->addSuccess($msg);
            } elseif(isset($results->Errors->LongMessage)) {
                $msg = $results->Errors->LongMessage;
                $this->messageManager->addError($results->Errors->LongMessage);
            } else {
                $msg = __('Failed to get the user token.');
                $this->messageManager->addError(__('Failed to get the user token.'));
            }
        } catch (\Exception $e) {
            $msg = __('Something went wrong.');
            $this->messageManager->addError($msg);
        }
        $resultPage = $this->_resultPageFactory->create();
        $block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('Webkul_MultiEbayStoreMageConnect::account/window-close.phtml')
                ->toHtml();
        $this->getResponse()->setBody($block);
    }
}
