<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model\ResourceModel\Rule;

/**
 * Class Collection
 */
class Collection extends \Amasty\CommonRules\Model\ResourceModel\Rule\Collection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\Shiprestriction\Model\Rule::class,
            \Amasty\Shiprestriction\Model\ResourceModel\Rule::class
        );
    }
}
