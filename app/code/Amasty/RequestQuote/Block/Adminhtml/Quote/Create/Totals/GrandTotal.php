<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Totals;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class GrandTotal extends DefaultTotals
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_RequestQuote::quote/create/totals/grandtotal.phtml';

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
    public function includeTax()
    {
        return $this->_taxConfig->displayCartTaxWithGrandTotal();
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
