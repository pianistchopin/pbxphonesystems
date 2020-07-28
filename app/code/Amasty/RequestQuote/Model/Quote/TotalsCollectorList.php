<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote;

class TotalsCollectorList extends \Magento\Quote\Model\Quote\TotalsCollectorList
{
    /**
     * @var array
     */
    private $availableCollectors = [
        'subtotal',
        'tax_subtotal',
        'shipping',
        'tax_shipping',
        'tax',
        'weee',
        'weee_tax',
        'grand_total'
    ];

    /**
     * @inheritdoc
     */
    public function getCollectors($storeId)
    {
        $collectors = parent::getCollectors($storeId);
        foreach ($collectors as $key => $collector) {
            if (!in_array($key, $this->availableCollectors)) {
                unset($collectors[$key]);
            }
        }

        return $collectors;
    }
}
