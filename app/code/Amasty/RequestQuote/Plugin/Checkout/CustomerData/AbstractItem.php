<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\AbstractItem as NativeAbstractItem;

class AbstractItem
{
    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $item;

    public function beforeGetItemData(
        NativeAbstractItem $subject,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $this->item = $item;

        return [$item];
    }

    /**
     * @param NativeAbstractItem $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetItemData(NativeAbstractItem $subject, $result)
    {
        if ($this->item->getOptionByCode('amasty_quote_price')) {
            if (isset($result['configure_url'])) {
                $result['configure_url'] = '#hide-element-' . $this->item->getId();
            }
        }

        return $result;
    }
}
