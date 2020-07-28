<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Checkout\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Data
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        QuoteRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Checkout\Helper\Data $subject
     * @param \Closure $proceed
     * @param $price
     *
     * @return float|mixed
     */
    public function aroundFormatPrice(
        $subject,
        \Closure $proceed,
        $price
    ) {
        if ($currency = $this->registry->registry('amasty_quote_currency')) {
            $result = $this->priceCurrency->format(
                $price,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->storeManager->getStore(),
                $currency
            );
        } else {
            $result = $proceed($price);
        }

        return $result;
    }
}
