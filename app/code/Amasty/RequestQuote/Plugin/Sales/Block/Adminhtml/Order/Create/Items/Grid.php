<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Sales\Block\Adminhtml\Order\Create\Items;

use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Amasty\RequestQuote\Model\ResourceModel\Quote as QuoteResource;
use Amasty\RequestQuote\Model\Source\Status;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid as MagentoGrid;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Grid
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    /**
     * @var null|string
     */
    private $currencyCode = null;

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
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
        $this->quoteResource = $quoteResource;
        $this->quoteSession = $quoteSession;
    }

    /**
     * @param MagentoGrid $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return float|mixed
     */
    public function aroundGetOriginalEditablePrice($subject, \Closure $proceed, $item)
    {
        $price = $proceed($item);
        if ($item->hasCustomPrice() && $this->isAmastyCustomPrice($item)) {
            $price = $this->quoteSession->getStore()->getBaseCurrency()->convert(
                $price,
                $this->priceCurrency->getCurrency(
                    null,
                    $this->getCurrencyCode($item)
                )
            );
        }

        return $price;
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
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    private function isAmastyCustomPrice(\Magento\Quote\Model\Quote\Item $item)
    {
        if ($item->getOptionByCode('amasty_quote_price')) {
            $result = true;
        } else {
            try {
                // detect amasty quote
                $quote = $this->quoteRepository->get($item->getQuote()->getId());
                $result = Status::CREATED == $quote->getStatus();
            } catch (NoSuchEntityException $entityException) {
                $result = false;
            }
        }

        return $result;
    }
}
