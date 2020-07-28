<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Item\Renderer\Actions;

class Remove extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Remove
{
    /**
     * @return string
     */
    public function getDeletePostJson()
    {
        /**
         * @TODO use own serializer
         */
        $deleteJson = json_decode($this->cartHelper->getDeletePostJson($this->getItem()), true);
        $deleteJson['action'] = $this->getUrl('amasty_quote/cart/delete');

        return json_encode($deleteJson);
    }
}
