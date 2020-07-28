<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Source;

/**
 * Class NoriTokenMode
 */
class NoriTokenMode implements \Magento\Framework\Option\ArrayInterface
{
    const NONE = 'none';
    const DISCARD = 'discard';
    const MIXED = 'mixed';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::DISCARD, 'label' => __('Discard')],
            ['value' => self::MIXED, 'label' => __('Mixed')]
        ];
    }
}
