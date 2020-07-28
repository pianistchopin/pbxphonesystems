<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\Component\Listing\Column;

class Stores extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if (array_key_exists($this->storeKey, $item)) {
            if (!$item[$this->storeKey]) {
                $item[$this->storeKey] = [0];
            }
            if (is_string($item[$this->storeKey])) {
                $item[$this->storeKey] = explode(',', $item[$this->storeKey]);
            }
        }

        return parent::prepareItem($item);
    }
}
