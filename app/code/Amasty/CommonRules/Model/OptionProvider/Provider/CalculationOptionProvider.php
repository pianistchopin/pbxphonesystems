<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\OptionProvider\Provider;

/**
 * Class CalculationOptionProvider
 */
class CalculationOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const CALC_REPLACE = 0;
    const CALC_ADD     = 1;
    const CALC_DEDUCT  = 2;
    const CALC_REPLACE_PRODUCT = 3;

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
                [
                    'label' => __('Replace'),
                    'value' => self::CALC_REPLACE
                ],
                [
                    'label' => __('Surcharge'),
                    'value' => self::CALC_ADD
                ],
                [
                    'label' => __('Discount'),
                    'value' => self::CALC_DEDUCT
                ],
                [
                    'label' => __('Partial Replace'),
                    'value' => self::CALC_REPLACE_PRODUCT
                ]
            ];
        }

        return $this->options;
    }
}
