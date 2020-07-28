<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class DefaultTotals extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Totals
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_RequestQuote::quote/create/totals/default.phtml';

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
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
}
