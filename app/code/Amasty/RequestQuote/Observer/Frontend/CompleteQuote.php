<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Observer\Frontend;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CompleteQuote implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Amasty\RequestQuote\Model\QuoteRepository
     */
    private $quoteRepository;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\RequestQuote\Model\QuoteRepository $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var \Amasty\RequestQuote\Model\Quote $amastyQuote */
            $amastyQuote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());
            $this->quoteRepository->updateStatus($amastyQuote, Status::COMPLETE);
        } catch (NoSuchEntityException $exception) {
            // order placed not on request quote
        }

        return $this;
    }
}
