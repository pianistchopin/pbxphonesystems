<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\OptionsProviders;

class Pages implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Constants defined for bar pages values
     */
    const HOME = 'home';

    const CATEGORY = 'category';

    const PRODUCT = 'product';

    const SEARCH = 'search';

    const CART = 'cart';

    /**
     * Shipping Bar is not allowed on Checkout page for security reasons.
     * Constant is need to prevent Bar appearance on Checkout page.
     */
    const CHECKOUT = 'checkout';

    const OTHER = 'other';
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
                'value' => self::HOME,
                'label' => __('Home Page')
            ],
            [
                'value' => self::CATEGORY,
                'label' => __('Category Pages')
            ],
            [
                'value' => self::PRODUCT,
                'label' => __('Product Pages')
            ],
            [
                'value' => self::SEARCH,
                'label' => __('Search Pages')
            ],
            [
                'value' => self::CART,
                'label' => __('Shopping Cart')
            ],
            [
                'value' => self::OTHER,
                'label' => __('Another Pages')
            ]
        ];
    }
}
