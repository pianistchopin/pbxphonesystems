<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Item;

class Configure extends \Magento\Framework\View\Element\Template
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $block->setSubmitRouteData(
                [
                    'route' => 'amasty_quote/cart/updateItemOptions',
                    'params' => ['id' => $this->getRequest()->getParam('id')],
                ]
            );
        }

        return parent::_prepareLayout();
    }
}
