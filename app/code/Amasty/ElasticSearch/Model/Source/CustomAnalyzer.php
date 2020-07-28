<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Source;

/**
 * Class CustomAnalyzer
 * @package Amasty\ElasticSearch\Model\Source
 */
class CustomAnalyzer implements \Magento\Framework\Option\ArrayInterface
{
    const DISABLED = 'disabled';
    const CHINESE = 'smartcn';
    const JAPANESE = 'kuromoji';
    const KOREAN = 'nori';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DISABLED, 'label' => __('Disabled')],
            ['value' => self::CHINESE, 'label' => __('Chinese')],
            ['value' => self::JAPANESE, 'label' => __('Japanese')],
            ['value' => self::KOREAN, 'label' => __('Korean')]
        ];
    }
}
