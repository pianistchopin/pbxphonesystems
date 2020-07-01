<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Ui\Component\Listing\Columns\PriceRule;

class OperationType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter.
     *
     * @return array
     */

    public function toOptionArray()
    {
        return [['value' => 'Fixed', 'label' => __('Fixed')], ['value' => 'Percent', 'label' => __('Percent')]];
    }
}
