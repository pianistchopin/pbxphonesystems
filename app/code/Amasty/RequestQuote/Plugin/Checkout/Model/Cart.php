<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Checkout\Model;

use Magento\Checkout\Model\Cart as NativeCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Amasty\RequestQuote\Api\QuoteRepositoryInterface;

class Cart
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        Session $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param NativeCart $subject
     * @param \Closure $proceed
     *
     * @return NativeCart
     *
     */
    public function aroundTruncate(
        NativeCart $subject,
        \Closure $proceed
    ) {
        try {
            $currentQuote = $subject->getQuote();
            $quote = $this->quoteRepository->get($currentQuote->getId());
            // clear id if amasty quote items removed
            if ($this->checkoutSession->getQuoteId() == $quote->getId()) {
                $this->checkoutSession->setQuoteId(null);
                $currentQuote->setIsActive(false);
                foreach ($currentQuote->getAllItems() as $quoteItem) {
                    if (!$quoteItem->getOptionByCode('amasty_quote_price')) {
                        $currentQuote->removeItem($quoteItem->getId());
                    }
                }
            } else {
                // quote not in cart
                $proceed();
            }
        } catch (NoSuchEntityException $exception) {
            // not amasty quote
            $proceed();
        }

        return $subject;
    }
}
