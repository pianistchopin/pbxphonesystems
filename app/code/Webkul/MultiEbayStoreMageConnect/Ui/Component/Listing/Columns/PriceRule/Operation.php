<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Ui\Component\Listing\Columns\PriceRule;

class Operation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter.
     *
     * @return array
     */

    public function toOptionArray()
    {
        return [['value' => 'Increase', 'label' => __('Increase')], ['value' => 'Decrease', 'label' => __('Decrease')]];
    }
}
