<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Item\Price;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Amasty\RequestQuote\Model\Source\Status;

class Renderer extends \Magento\Checkout\Block\Item\Price\Renderer
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;

    public function __construct(
        \Magento\Tax\Model\Config $taxConfig,
        PriceCurrencyInterface $priceCurrency,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->taxConfig = $taxConfig;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * @param $price
     * @param bool $delimiter
     *
     * @return float
     */
    public function convertPrice($price, $delimiter = true)
    {
        $currency = null;
        $options = [];
        if ($this->getItem()->getQuote()->getStatus() != Status::CREATED) {
            $currency = $this->getItem()->getQuote()->getCurrency()->getQuoteCurrencyCode();
        } else {
            $options['symbol'] = '';
        }

        if (!$delimiter) {
            $options['format'] = '###0.00';
        }

        return $this->priceCurrency
            ->getCurrency($this->getItem()->getQuote()->getStore(), $currency)
            ->formatPrecision($price, PriceCurrencyInterface::DEFAULT_PRECISION, $options, false);
    }

    /**
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->taxConfig->priceIncludesTax();
    }

    /**
     * @return float
     */
    public function getInputPrice()
    {
        $price = !$this->getItem()->hasCustomPrice() && $this->priceIncludesTax()
            ? $this->getItem()->getPriceInclTax()
            : $this->getItem()->getCalculationPrice();

        return $this->convertPrice(
            $price,
            false
        );
    }
}
