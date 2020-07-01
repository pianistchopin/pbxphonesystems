<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class EbayGlobalSitesList implements \Magento\Framework\Option\ArrayInterface
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
        $eBayGlobalSites = [['value' => '0','label' => 'eBay United States'],
         ['value' => '3','label' => 'eBay UK'],
         ['value' => '2','label' => 'eBay Canada (English)'],
         ['value' => '15','label' => 'eBay Australia'],
         ['value' => '16','label' => 'eBay Austria'],
         ['value' => '23','label' => 'eBay Belgium (French)'],
         ['value' => '71','label' => 'eBay France'],
         ['value' => '77','label' => 'eBay Germany'],
         ['value' => '100','label' => 'eBay Motors'],
         ['value' => '101','label' => 'eBay Italy'],
         ['value' => '123','label' => 'eBay Belgium (Dutch)'],
         ['value' => '146','label' => 'eBay Netherlands'],
         ['value' => '186','label' => 'eBay Spain'],
         ['value' => '193','label' => 'eBay Switzerland'],
         ['value' => '201','label' => 'eBay Hong Kong'],
         ['value' => '203','label' => 'eBay India'],
         ['value' => '205','label' => 'eBay Ireland'],
         ['value' => '207','label' => 'eBay Malaysia'],
         ['value' => '210','label' => 'eBay Canada (French)'],
         ['value' => '211','label' => 'eBay Philippines'],
         ['value' => '201','label' => 'eBay Poland'],
         ['value' => '201','label' => 'eBay Singapore'],
        ];

        return $eBayGlobalSites;
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
