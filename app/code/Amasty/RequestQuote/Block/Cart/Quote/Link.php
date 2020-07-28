<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Quote;

class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string
     */
    public function getQuoteSubmitUrl()
    {
        return $this->getUrl('amasty_quote/cart/submit');
    }
}
