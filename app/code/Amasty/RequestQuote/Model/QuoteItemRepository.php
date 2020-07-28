<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model;

use Amasty\Base\Model\Serializer;
use Amasty\RequestQuote\Api\Data\QuoteItemInterface;
use Amasty\RequestQuote\Api\QuoteItemRepositoryInterface;
use Amasty\RequestQuote\Model\ResourceModel\Quote\Item as ItemResource;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\ItemFactory;

/**
 * Class QuoteItemRepository
 */
class QuoteItemRepository implements QuoteItemRepositoryInterface
{
    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ItemResource
     */
    private $itemResource;

    public function __construct(
        ItemFactory $itemFactory,
        Serializer $serializer,
        ItemResource $itemResource
    ) {
        $this->itemFactory = $itemFactory;
        $this->serializer = $serializer;
        $this->itemResource = $itemResource;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerNote($quoteItemId, $note)
    {
        return $this->updateAdditinalData($quoteItemId, QuoteItemInterface::CUSTOMER_NOTE_KEY, $note);
    }

    /**
     * {@inheritdoc}
     */
    public function addAdminNote($quoteItemId, $note)
    {
        return $this->updateAdditinalData($quoteItemId, QuoteItemInterface::ADMIN_NOTE_KEY, $note);
    }

    /**
     * @param int $quoteItemId
     * @param string $key
     * @param string $note
     *
     * @return bool
     */
    private function updateAdditinalData($quoteItemId, $key, $note)
    {
        $result = true;
        try {
            /** @var Item $quote */
            $item = $this->itemFactory->create()
                ->load($quoteItemId);
            $additinalData = $item->getAdditionalData()
                ? $this->serializer->unserialize($item->getAdditionalData())
                : [];
            $additinalData[$key] = $note;
            $this->itemResource->updateAdditinalData(
                $item->getId(),
                $this->serializer->serialize($additinalData)
            );
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
}
