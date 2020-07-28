<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Category implements ArrayInterface
{
    const NONE = 0;
    const SYSTEM_CATEGORY_ID = 1;
    const ROOT_LEVEL = 1;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getChildren(self::SYSTEM_CATEGORY_ID, self::ROOT_LEVEL);
    }

    /**
     * @param $parentCategoryId
     * @param $level
     * @return array
     */
    private function getChildren($parentCategoryId, $level)
    {
        $options[self::NONE] = __('None');
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToFilter('level', $level);
        $collection->addAttributeToFilter('parent_id', $parentCategoryId);
        $collection->addAttributeToFilter('is_active', 1);
        $collection->setOrder('position', 'asc');

        foreach ($collection as $category) {
            if ($category->getLevel() > self::ROOT_LEVEL) {
                $options[$category->getId()] =
                    str_repeat(". ", max(0, ($category->getLevel() - 1) * 3)) . $category->getName();
            }
            if ($category->hasChildren()) {
                $options = array_replace($options, $this->getChildren($category->getId(), $category->getLevel() + 1));
            }
        }

        return $options;
    }
}
