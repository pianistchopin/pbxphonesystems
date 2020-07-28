<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Source;

/**
 * Class KuromojiTokenMode
 */
class KuromojiTokenMode implements \Magento\Framework\Option\ArrayInterface
{
    const NORMAL = 'normal';
    const SEARCH = 'search';
    const EXTENDED = 'extended';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NORMAL, 'label' => __('Normal')],
            ['value' => self::SEARCH, 'label' => __('Search')],
            ['value' => self::EXTENDED, 'label' => __('Extended')]
        ];
    }
}
