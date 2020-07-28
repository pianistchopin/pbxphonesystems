<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\OptionsProviders;

class GoalSource implements \Magento\Framework\Data\OptionSourceInterface
{
    const USE_GOAL = 0;
    const USE_FREE_SHIP_CONFIG = 1;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::USE_GOAL,
                'label' => __('Specify manually')
            ],
            [
                'value' => self::USE_FREE_SHIP_CONFIG,
                'label' => __('Free Shipping Configuration')
            ],
        ];
    }
}
