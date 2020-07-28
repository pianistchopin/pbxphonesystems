<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Quote;

class Details extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string
     */
    public function getJsLayout()
    {
        /**
         * @TODO use own serializer
         */
        return json_encode($this->jsLayout, JSON_HEX_TAG);
    }
}
