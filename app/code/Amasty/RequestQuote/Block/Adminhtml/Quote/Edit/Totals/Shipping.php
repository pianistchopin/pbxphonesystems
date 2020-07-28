<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Shipping extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals\DefaultTotals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/create/totals/shipping.phtml';

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return $this->taxConfig->displayCartShippingBoth();
    }

    /**
     * @return bool
     */
    public function displayIncludeTax()
    {
        return $this->taxConfig->displayCartShippingInclTax();
    }

    /**
     * @return float
     */
    public function getShippingIncludeTax()
    {
        return $this->getTotal()->getShippingInclTax();
    }

    /**
     * Get shipping amount exclude tax
     *
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
