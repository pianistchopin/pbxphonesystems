<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */

namespace Amasty\Pgrid\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddQtySoldFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Added filter by use_config_manage_stock to collection
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param string $field
     * @param array $condition
     */
    public function addFilter(Collection $collection, $field = null, $condition = null)
    {
        if (!$collection->getFlag('from')) {
            $collection->getSelect()->joinLeft(
                ['amasty_qty_sold' => $this->resource->getTableName('amasty_pgrid_qty_sold')],
                'e.entity_id=amasty_qty_sold.product_id',
                ['qty_sold']
            );
            $collection->setFlag('from', true);
        }

        if (isset($condition['gteq'])) {
            $collection->getSelect()->where(
                'amasty_qty_sold.qty_sold >= ?',
                (float)$condition['gteq']
            );
        }
        if (isset($condition['lteq'])) {
            $collection->getSelect()->where(
                'amasty_qty_sold.qty_sold <= ?',
                (float)$condition['lteq']
            );
        }
    }
}
