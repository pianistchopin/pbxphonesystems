<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Checkout\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic as NativeGeneric;

class Generic
{
    /**
     * @param NativeGeneric $subject
     * @param string $html
     *
     * @return string
     */
    public function afterToHtml(NativeGeneric $subject, $html)
    {
        if ($subject->getItem()->getOptionByCode('amasty_quote_price')) {
            $html = '';
        }

        return $html;
    }
}
