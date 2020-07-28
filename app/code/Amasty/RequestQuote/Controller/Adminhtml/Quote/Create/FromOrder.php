<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class FromOrder
 */
class FromOrder extends Action
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::clone_order';

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $quoteSession;

    /**
     * @var \Amasty\RequestQuote\Api\QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $session,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->quoteSession = $session;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $redirectPath = 'amasty_quote/quote/index';
        $params = [];
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($orderId = $this->getRequest()->getParam('order_id', false)) {
            $this->quoteSession->clearStorage();
            try {
                $order = $this->orderRepository->get($orderId);
                $magentoQuote = $this->quoteRepository->getMagentoQuote($order->getQuoteId());
                $this->quoteSession->initFromQuote($magentoQuote);
                $redirectPath = 'amasty_quote/quote_create/index';
                $params = [
                    'customer_id' => $magentoQuote->getCustomerId(),
                    'store_id' => $magentoQuote->getStoreId()
                ];
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong'));
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Order does not exist'));
        }

        return $resultRedirect->setPath($redirectPath, $params);
    }
}
