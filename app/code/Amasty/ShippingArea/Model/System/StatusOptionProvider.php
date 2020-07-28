<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model\System;

use Magento\Framework\Option\ArrayInterface;

class StatusOptionProvider implements ArrayInterface
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    /**
     * @var array|null
     */
    private $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::STATUS_INACTIVE, 'label' => __('Inactive')],
                ['value' => self::STATUS_ACTIVE, 'label' => __('Active')],
            ];
        }

        return $this->options;
    }
}
