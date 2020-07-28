<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote;

use Magento\Framework\Registry;

class Configuration extends \Magento\Backend\Block\Template
{
    /**
     * @var Registry
     */
    private $quoteSession;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $currency;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        \Magento\Framework\Json\EncoderInterface $encoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quoteSession = $quoteSession;
        $this->currency = $currency;
        $this->jsonEncoder = $encoder;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    private function getQuote()
    {
        return $this->quoteSession->getQuote();
    }

    /**
     * @return string
     */
    public function getQuoteConfigJson()
    {
        $data = [];
        if ($this->getQuote()->getCustomerId()) {
            $data['customer_id'] = $this->getQuote()->getCustomerId();
        }
        if ($this->getQuote()->getStoreId() !== null) {
            $data['store_id'] = $this->getQuote()->getStoreId();
            $currency = $this->currency->getCurrency($this->getQuote()->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
        }

        return $this->jsonEncoder->encode($data);
    }
}
