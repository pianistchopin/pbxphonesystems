<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Structure\EntityBuilder\Product;

use Amasty\ElasticSearch\Api\Data\Indexer\Structure\EntityBuilderInterface;
use Amasty\ElasticSearch\Model\Indexer\Structure\EntityBuilder\Product;

class CategoryData implements EntityBuilderInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function buildEntityFields()
    {
        $fields = [];
        $categoryIds = $this->categoryCollectionFactory->create()->getAllIds();

        foreach ($categoryIds as $categoryId) {
            $fields['category_position_' . $categoryId] = [
                'type' => Product::ATTRIBUTE_TYPE_INT,
                'index' => false
            ];
        }

        return $fields;
    }
}
