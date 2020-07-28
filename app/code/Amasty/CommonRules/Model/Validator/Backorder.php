<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Validator;

use Amasty\CommonRules\Model\Rule;

/**
 * Backorder Validation.
 */
class Backorder implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate($rule, $items)
    {
        switch ($rule->getOutOfStock()) {
            case Rule::BACKORDERS_ONLY:
                return $this->checkItemsBackorder($items, true);
            case Rule::NON_BACKORDERS:
                return $this->checkItemsBackorder($items, false);
        }

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @param bool $backorderFlag true - only backorder, false - only without backorder
     *
     * @return bool
     */
    protected function checkItemsBackorder($items, $backorderFlag)
    {
        foreach ($items as $item) {
            if ((bool)$item->getBackorders() !== $backorderFlag) {
                return false;
            }
        }

        return true;
    }
}
