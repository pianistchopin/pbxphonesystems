<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Ui\Component\Listing\Columns\PriceRule;

class StoreName implements \Magento\Framework\Option\ArrayInterface
{

    public function __construct(
        \Webkul\MultiEbayStoreMageConnect\Model\Ebayaccounts $accounts
    ) {
        $this->accounts = $accounts;
    }

    /**
     * Options getter.
     *
     * @return array
     */

    public function toOptionArray()
    {
        $collection = $this->accounts->getCollection();
        $ebayStores = [];
        foreach ($collection as $ebayStore) {
            $ebayStores[] = [
                'value' => $ebayStore->getId(),
                'label' => $ebayStore->getStoreName()
            ];
        }
        return $ebayStores;
    }
}
