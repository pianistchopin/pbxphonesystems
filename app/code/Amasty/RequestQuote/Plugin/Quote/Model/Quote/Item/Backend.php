<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Quote\Model\Quote\Item;

use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Amasty\RequestQuote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Store\Model\StoreManagerInterface;

class Backend extends \Amasty\RequestQuote\Plugin\Quote\Model\Quote\Item
{
    /**
     * @var QuoteSession
     */
    private $quoteSession;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        QuoteRepositoryInterface $quoteRepository,
        QuoteResource $quoteResource,
        QuoteSession $quoteSession
    ) {
        parent::__construct($priceCurrency, $storeManager, $quoteRepository, $quoteResource);
        $this->quoteSession = $quoteSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return null|string
     */
    public function getCurrencyCode(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->quoteSession->getCurrencyId() && $this->quoteSession->getCurrencyId() != 'false'
            ? $this->quoteSession->getCurrencyId()
            : $item->getQuote()->getQuoteCurrencyCode();
    }

    /**
     * @inheritdoc
     */
    public function getCurrencyFrom($item)
    {
        return $this->quoteSession->getStoreId()
            ? $this->quoteSession->getStore()->getBaseCurrency()
            : parent::getCurrencyFrom($item);
    }
}
