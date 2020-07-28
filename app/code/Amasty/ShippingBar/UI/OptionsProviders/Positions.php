<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\OptionsProviders;

class Positions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Constants defined for bar position values
     */
    const TOP_UNFIXED = 10;

    const TOP_FIXED = 15;

    const BOTTOM_UNFIXED = 20;

    const BOTTOM_FIXED = 25;
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TOP_UNFIXED,
                'label' => __('Page Top, unfixed')
            ],
            [
                'value' => self::TOP_FIXED,
                'label' => __('Page Top, fixed')
            ],
            [
                'value' => self::BOTTOM_UNFIXED,
                'label' => __('Page Bottom, unfixed')
            ],
            [
                'value' => self::BOTTOM_FIXED,
                'label' => __('Page Bottom, fixed')
            ]
        ];
    }
}
