<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;

abstract class AbstractCreate extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    protected $sessionQuote;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->sessionQuote = $sessionQuote;
        parent::__construct($context, $data);
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    protected function _getSession()
    {
        return $this->sessionQuote;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getStore()
        );
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getItemPrice(Product $product)
    {
        $price = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
        return $this->convertPrice($price);
    }

    /**
     * @param int|float $value
     * @param bool $format
     * @return string|int|float
     */
    public function convertPrice($value, $format = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $value,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->getStore()
            )
            : $this->priceCurrency->convert($value, $this->getStore());
    }

    /**
     * @param $item
     *
     * @return Product
     */
    public function getProduct($item)
    {
        if ($item instanceof Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }

        return $product;
    }
}
