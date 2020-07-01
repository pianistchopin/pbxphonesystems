<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class DispatchTime implements \Magento\Framework\Option\ArrayInterface
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
        $dispatchTime = [['value' => '0','label' => '0 Days'],
                            ['value' => '1','label' => '1 Days'],
                            ['value' => '2','label' => '2 Days'],
                            ['value' => '3','label' => '3 Days'],
                            ['value' => '4','label' => '4 Days'],
                            ['value' => '5','label' => '5 Days'],
                            ['value' => '10','label' => '10 Days'],
                            ['value' => '15','label' => '15 Days'],
                            ['value' => '20','label' => '20 Days'],
                            ['value' => '30','label' => '30 Days'], ];

        return $dispatchTime;
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
