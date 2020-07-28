<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Observer\Backend;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\Event\ObserverInterface;

class CompleteQuote implements ObserverInterface
{
    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $quoteSession;

    /**
     * @var \Amasty\RequestQuote\Api\QuoteRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository
    ) {
        $this->quoteSession = $quoteSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getEvent()->getQuote()->getId() == $this->quoteSession->getQuoteId()) {
            $this->quoteRepository->updateStatus($this->quoteSession->getQuote(), Status::COMPLETE);
        }
        return $this;
    }
}
