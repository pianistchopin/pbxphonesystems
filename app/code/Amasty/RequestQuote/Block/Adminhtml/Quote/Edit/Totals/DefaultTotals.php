<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class DefaultTotals extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/create/totals/default.phtml';

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getSession()->getStore();
    }
}
