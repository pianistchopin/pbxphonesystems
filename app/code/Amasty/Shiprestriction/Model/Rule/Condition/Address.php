<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model\Rule\Condition;

/**
 * Class Address
 */
class Address extends \Amasty\CommonRules\Model\Rule\Condition\Address
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();

        $attributes = $this->getAttributeOption();
        unset($attributes['shipping_method']);
        $this->setAttributeOption($attributes);

        return $this;
    }
}
