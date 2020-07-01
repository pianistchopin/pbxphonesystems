<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;

use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\EbayAccount;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Helper\Data as HelperData;

class GenerateEbaySession extends EbayAccount
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
        \Magento\Backend\App\Action\Context $context,
        PageFactory $_resultPageFactory,
        JsonFactory $resultJsonFactory,
        HelperData $helperData
    ) {
        $this->_resultPageFactory = $_resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * MultiEbayStoreMageConnect Detail page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $sellereBayConfig = $this->_helperData->geteBayConfiguration();
        $client = $this->_helperData->getEbayAPI();
        $ruName = $sellereBayConfig['app_ru_name'];
        $newParams = [
            'Version' => 891,
            'RuName' => $ruName
        ];
        $results = $client->GetSessionID($newParams);
        $error = 1;
        $sessionId = null;
        if (isset($results->Ack) && $results->Ack === 'Success') {
            $sessionId = $results->SessionID;
            $error = 0;
        } elseif (isset($results->faultstring)) {
            $error = $results->faultstring;
        } else {
            $error = json_encode($results);
        }
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData(['error' => $error, 'sessionId' => $sessionId, 'ruName' => $ruName]);
    }
}
