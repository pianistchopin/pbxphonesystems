<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Pgrid\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class AddQuantityFieldToCollection
 */
class AddCategoryFilterToCollection implements AddFilterToCollectionInterface
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

    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if (isset($condition['eq']) && $condition['eq'] === 'no_category') {
            $categoryTableName = 'amasty_category';
            $from = $collection->getSelect()->getPart('from');
            if (!isset($from[$categoryTableName])) {
                $collection->getSelect()->joinLeft(
                    [$categoryTableName => $this->resource->getTableName('catalog_category_product')],
                    'e.entity_id=amasty_category.product_id',
                    ['category_id']
                );

                $collection->getSelect()->where('amasty_category.category_id IS NULL');
            }
        } else {
            $collection->addCategoriesFilter($condition);
        }
    }
}
