<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Shipping extends DefaultTotals
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_RequestQuote::quote/create/totals/shipping.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $sessionQuote, $priceCurrency, $salesData, $salesConfig, $data);
    }

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return $this->_taxConfig->displayCartShippingBoth();
    }

    /**
     * @return bool
     */
    public function displayIncludeTax()
    {
        return $this->_taxConfig->displayCartShippingInclTax();
    }

    /**
     * @return float
     */
    public function getShippingIncludeTax()
    {
        return $this->getTotal()->getShippingInclTax();
    }

    /**
     * @return float
     */
    public function getShippingExcludeTax()
    {
        return $this->getTotal()->getValue();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getIncludeTaxLabel()
    {
        return __(
            'Shipping Incl. Tax (%1)',
            $this->escapeHtml($this->getQuote()->getShippingAddress()->getShippingDescription())
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getExcludeTaxLabel()
    {
        return __(
            'Shipping Excl. Tax (%1)',
            $this->escapeHtml($this->getQuote()->getShippingAddress()->getShippingDescription())
        );
    }
}
