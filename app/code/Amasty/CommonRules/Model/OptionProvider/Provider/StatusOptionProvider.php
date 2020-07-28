<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

/**
 * OptionProvider
 */
class StatusOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const ACTIVE  = 1;
    const INACTIVE = 0;

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
                ['value' => self::ACTIVE, 'label' => __('Active')],
                ['value' => self::INACTIVE, 'label' => __('Inactive')],
            ];
        }

        return $this->options;
    }
}
