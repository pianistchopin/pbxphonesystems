<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\QuoteRepository;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Exception\InputException;

class SaveHandler
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    private $quoteResourceModel;

    /**
     * @var \Magento\Quote\Model\Quote\Item\CartItemPersister
     */
    private $cartItemPersister;

    public function __construct(
        \Amasty\RequestQuote\Model\ResourceModel\Quote $quoteResource,
        \Magento\Quote\Model\Quote\Item\CartItemPersister $cartItemPersister
    ) {
        $this->quoteResourceModel = $quoteResource;
        $this->cartItemPersister = $cartItemPersister;
    }

    /**
     * @param CartInterface $quote
     * @return CartInterface
     * @throws InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $items = $quote->getItems();

        if ($items) {
            foreach ($items as $item) {
                /** @var \Magento\Quote\Model\Quote\Item $item */
                if (!$item->isDeleted()) {
                    $quote->setLastAddedItem($this->cartItemPersister->save($quote, $item));
                }
            }
        }

        $this->quoteResourceModel->save($quote->collectTotals());

        return $quote;
    }

    /**
     * @param \Amasty\RequestQuote\Model\Quote $quote
     * @param $status
     * @return mixed
     */
    public function updateStatus(\Amasty\RequestQuote\Model\Quote $quote, $status)
    {
        $this->quoteResourceModel->updateStatus($quote, $status);
        return $this;
    }

    /**
     * @param \Amasty\RequestQuote\Model\Quote $quote
     * @param $remarks
     * @return mixed
     */
    public function updateRemarks(\Amasty\RequestQuote\Model\Quote $quote, $remarks)
    {
        $this->quoteResourceModel->updateRemarks($quote, $remarks);
        return $this;
    }
}
