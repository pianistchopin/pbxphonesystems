<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\ResourceModel\Quote;

use Magento\Quote\Model\ResourceModel\Quote\Item as MagentoItem;

/**
 * Class Item
 */
class Item extends MagentoItem
{
    /**
     * @param int $quoteItemId
     * @param string $additionalData
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateAdditinalData($quoteItemId, $additionalData)
    {
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), [
            'item_id' => $quoteItemId,
            'additional_data' => $additionalData
        ], ['additional_data']);
        return $this;
    }
}
