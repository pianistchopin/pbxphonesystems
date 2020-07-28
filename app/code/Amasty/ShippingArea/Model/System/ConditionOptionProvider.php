<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model\System;

class ConditionOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const CONDITION_ALL = 0;
    const CONDITION_INCLUDE = 1;
    const CONDITION_EXCLUDE = 2;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::CONDITION_ALL, 'label' => __('All')],
                ['value' => self::CONDITION_INCLUDE, 'label' => __('Include')],
                ['value' => self::CONDITION_EXCLUDE, 'label' => __('Exclude')]
            ];
        }

        return $this->options;
    }
}
