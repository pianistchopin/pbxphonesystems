<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class EbayEvents
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'ItemSold', 'label' => __('Item Sold')],
            ['value' => 'ItemListed', 'label' => __('Item Create')],
            ['value' => 'ItemRevised', 'label' => __('Item Revised')],
            ['value' => 'ItemClosed', 'label' => __('Item Closed')]
        ];
        return $data;
    }
}
