<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\EventNotification;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ebay;
use Webkul\MultiEbayStoreMageConnect\Model\Notification;

/**
 * Index class of EventNotification
 */
class EbayListner extends Action
{
    public $session;

    /**
     * @param Context                                     $context
     * @param PageFactory                                 $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\MultiEbayStoreMageConnect\Logger\Logger $logger,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Index the Shopfollower
     */
    public function execute()
    {
        try {
            $this->logger->info(' magento controller hitted test');
            $this->dataHelper->includeEbayFiles();
            $ebayConfig = $this->dataHelper->geteBayConfiguration();

            $this->session = new Ebay\eBaySession($ebayConfig['dev'], $ebayConfig['app'], $ebayConfig['cert']);

            $this->logger->info(' start ');
            $server = new \SOAPServer(null, ['uri' => 'urn:ebay:apis:eBLBaseComponents']);
            $server->setClass('Webkul\MultiEbayStoreMageConnect\Model\Notification', $this->session, true);
            $server->handle();
            $this->logger->info(' finished ');
        } catch (\Exception $e) {
            $this->logger->info('EbayListner : '.$e->getMessage());
        }
    }
}
