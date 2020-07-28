<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Model\System\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

/**
 * Class RelatedTerms
 */
class RelatedTerms implements ArrayInterface
{
    const DISABLED = 0;
    const SHOW_ALWAYS = 1;
    const SHOW_ONLY_WITHOUT_RESULTS = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::DISABLED => __('No, Disabled'),
            self::SHOW_ALWAYS => __('Yes, Show Always'),
            self::SHOW_ONLY_WITHOUT_RESULTS => __('Yes, Show Only when search returns 0 results'),
        ];

        return $options;
    }
}
