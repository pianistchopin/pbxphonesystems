<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote\Item;

/**
 * Class Repository
 *
 * use physical class instead of virtual type because https://github.com/magento/magento2/issues/14950
 */
class Repository extends \Magento\Quote\Model\Quote\Item\Repository
{
    public function __construct(
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        array $cartItemProcessors = []
    ) {
        parent::__construct($quoteRepository, $productRepository, $itemDataFactory, $cartItemProcessors);
    }
}
