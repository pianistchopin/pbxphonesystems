<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model\ResourceModel\Area;

use Amasty\ShippingArea\Api\Data\AreaInterface;

/**
 * @method \Amasty\ShippingArea\Model\Area[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'area_id';

    protected function _construct()
    {
        $this->_init(\Amasty\ShippingArea\Model\Area::class, \Amasty\ShippingArea\Model\ResourceModel\Area::class);
    }

    /**
     * Filter only Enabled Areas
     *
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter(AreaInterface::IS_ENABLED, 1);
        return $this;
    }
}
