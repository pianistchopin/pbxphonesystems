<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Exist
 */
class Exist extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::createFromMagentoQuote';

    /**
     * @var \Amasty\RequestQuote\Api\QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $quoteSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $session,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->quoteSession = $session;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($magentoQuoteId = $this->getRequest()->getParam('quote_id', false)) {
            $this->quoteSession->clearStorage();
            try {
                $magentoQuote = $this->quoteRepository->getMagentoQuote($magentoQuoteId);
                $this->quoteSession->initFromQuote($magentoQuote);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong'));
                $this->logger->critical($e);
                return $resultRedirect->setPath('amasty_quote/quote/index');
            }

            return $resultRedirect->setPath(
                'amasty_quote/quote_create/index',
                [
                    'customer_id' => $magentoQuote->getCustomerId(),
                    'store_id' => $magentoQuote->getStoreId()
                ]
            );
        }
        $this->messageManager->addErrorMessage(__('Requested quote does not exist'));
        return $resultRedirect->setPath('amasty_quote/quote/index');
    }
}
