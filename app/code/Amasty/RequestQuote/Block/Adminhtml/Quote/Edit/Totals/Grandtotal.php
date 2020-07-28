<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Grandtotal extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals\DefaultTotals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/create/totals/grandtotal.phtml';

    /**
     * @return bool
     */
    public function includeTax()
    {
        return $this->taxConfig->displayCartTaxWithGrandTotal();
    }

    /**
     * @return float
     */
    public function getTotalExclTax()
    {
        $excl = $this->getTotals()['grand_total']->getValue() - $this->getTotals()['tax']->getValue();
        $excl = max($excl, 0);
        return $excl;
    }
}
