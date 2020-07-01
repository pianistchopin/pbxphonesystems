<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class ListingDuration implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return options array.
     *
     * @param int $store
     *
     * @return array
     */
    public function toOptionArray($store = null)
    {
        $listingDuration = [['value' => 'GTC','label' => 'Good Till Canceled'],
                            ['value' => 'Days_1','label' => '1 Day'],
                            ['value' => 'Days_3','label' => '3 Day'],
                            ['value' => 'Days_5','label' => '5 Day'],
                            ['value' => 'Days_7','label' => '7 Day'],
                            ['value' => 'Days_10','label' => '10 Day'],
                            ['value' => 'Days_30','label' => '30 Day'], ];

        return $listingDuration;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $optionList = $this->toOptionArray();
        $optionArray = [];
        foreach ($optionList as $option) {
            $optionArray[$option['value']] = $option['label'];
        }

        return $optionArray;
    }
}
