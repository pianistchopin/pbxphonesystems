<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Tax\Model\Sales\Total\Quote;

use Amasty\RequestQuote\Model\ResourceModel\Quote as QuoteResource;

class CommonTaxCollector
{
    /**
     * @var array
     */
    private $amastyQuotes = [];

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    public function __construct(QuoteResource $quoteResource)
    {
        $this->quoteResource = $quoteResource;
    }

    /**
     * @param \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector $subject
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param $priceIncludesTax
     * @param $useBaseCurrency
     * @param null $parentCode
     *
     * @return array
     */
    public function beforeMapItem(
        \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector $subject,
        \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency,
        $parentCode = null
    ) {
        if ($item->getCustomPrice() &&
            $item->getQuote() &&
            $this->isAmastyQuote($item->getQuote()->getId())
        ) {
            $priceIncludesTax = false;
        }

        return [$itemDataObjectFactory, $item, $priceIncludesTax, $useBaseCurrency, $parentCode];
    }

    /**
     * @param int $quoteId
     *
     * @return bool
     */
    public function isAmastyQuote($quoteId)
    {
        if (!isset($this->amastyQuotes[$quoteId])) {
            $this->amastyQuotes[$quoteId] = $this->quoteResource->isAmastyQuote($quoteId);
        }

        return $this->amastyQuotes[$quoteId];
    }
}
