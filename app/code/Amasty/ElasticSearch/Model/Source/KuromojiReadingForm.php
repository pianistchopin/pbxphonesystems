<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Source;

/**
 * Class KuromojiReadingForm
 */
class KuromojiReadingForm implements \Magento\Framework\Option\ArrayInterface
{
    const NONE = 0;
    const ROMAJI = 1;
    const KATAKANA = 2;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::ROMAJI, 'label' => __('Romaji')],
            ['value' => self::KATAKANA, 'label' => __('Katakana')]
        ];
    }
}
