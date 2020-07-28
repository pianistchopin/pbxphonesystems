<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Yesnocustom
 */
class Yesnocustom implements ArrayInterface
{
    const NO = 0;
    const INSTANTLY = 2;
    const CUSTOM = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CUSTOM, 'label' => __('Yes(customize)')],
            ['value' => self::INSTANTLY, 'label' => __('Yes(instantly)')],
            ['value' => self::NO, 'label' => __('No')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::NO => __('No'),
            self::INSTANTLY => __('Yes(instantly)'),
            self::CUSTOM => __('Yes(customize)')
        ];
    }
}
