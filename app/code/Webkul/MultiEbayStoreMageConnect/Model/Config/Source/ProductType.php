<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

/**
 * Used in creating options for getting product type value.
 */
class ProductType
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'simple', 'label' => __('Simple')],
            ['value' => 'configurable', 'label' => __('Configurable')],
            ['value' => 'bundle', 'label' => __('Bundle')],
            ['value' => 'grouped', 'label' => __('Grouped')]
        ];
    }
}
