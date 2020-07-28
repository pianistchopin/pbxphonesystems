<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\QuoteRepository;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;

class LoadHandler
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    public function __construct(
        CartExtensionFactory $cartExtensionFactory
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    /**
     * @param CartInterface $quote
     * @return CartInterface
     */
    public function load(CartInterface $quote)
    {

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote->setItems($quote->getAllVisibleItems());
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }
        $quote->setExtensionAttributes($cartExtension);

        return $quote;
    }
}
