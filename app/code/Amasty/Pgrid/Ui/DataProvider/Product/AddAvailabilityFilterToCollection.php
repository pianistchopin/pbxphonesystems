<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */

namespace Amasty\Pgrid\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;
use Amasty\Pgrid\Ui\Component\Listing\Column\Availability;


class AddAvailabilityFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * @var Availability
     */
    protected $availabilityColumn;

    public function __construct(
        Availability $availabilityColumn
    ) {
        $this->availabilityColumn = $availabilityColumn;
    }


    /**
     * Added filter by use_config_manage_stock to collection
     *
     * @param Collection $collection
     * @param string $field
     * @param array $condition
     */
    public function addFilter(Collection $collection, $field = null, $condition = null)
    {
        if ($collection->getFlag('amasty_instock_filter')) {
            return;
        }

        $collection->joinField(
            'amasty_availability',
            'cataloginventory_stock_item',
            $this->availabilityColumn->getAvailableExpression(),
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $collection->getSelect()->where($this->availabilityColumn->getAvailableExpression() . '= ?', $condition['eq']);
        $collection->setFlag('amasty_instock_filter', 1);
    }
}
