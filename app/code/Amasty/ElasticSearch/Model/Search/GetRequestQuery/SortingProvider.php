<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Search\GetRequestQuery;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\Search\RequestInterface;
use \Magento\Eav\Model\Config;
use \Magento\Customer\Model\Session;
use \Magento\Framework\Registry;
use Magento\Store\Model\StoreManager;

/**
 * Class SortingProvider
 */
class SortingProvider
{
    /**
     * List of fields that need to skipp by default.
     */
    private $skippedFields = ['entity_id'];

    /**
     * Default mapping for special fields.
     */
    private $map = ['relevance' => '_score'];

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var StoreManager
     */
    private $storeManager;

    public function __construct(
        Config $eavConfig,
        Session $customerSession,
        Registry $registry,
        StoreManager $storeManager,
        array $skippedFields = [],
        array $map = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->customerSession = $customerSession;
        $this->coreRegistry = $registry;
        $this->storeManager = $storeManager;
        $this->skippedFields = array_merge( $this->skippedFields, $skippedFields);
        $this->map = array_merge($this->map, $map);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(RequestInterface $request)
    {
        if (!method_exists($request, 'getSort')) {
            return [];
        }

        $sortings = [];
        foreach ($request->getSort() as $item) {
            if (in_array($item['field'], $this->skippedFields)) {
                continue;
            }
            $attributeCode = $item['field'];
            $attribute = $attribute = $this->eavConfig->getAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attributeCode
            );
            $fieldName = $this->getFieldName($attributeCode);
            if (isset($this->map[$fieldName])) {
                $fieldName = $this->map[$fieldName];
            }
            if ($attribute->getUsedForSortBy()
                && !in_array($attribute->getBackendType(), ['int', 'smallint', 'decimal'], true)
            ) {
                $fieldName .= '.sort_' . $attributeCode;
            }
            $sortings[] = [
                $fieldName => [
                    'order' => strtolower($item['direction'])
                ]
            ];
        }

        return $sortings;
    }

    /**
     * @param $attributeCode
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFieldName($attributeCode)
    {
        if ($attributeCode === 'price') {
            $customerGroupId = $this->customerSession->getCustomerGroupId();
            return 'price_' . $customerGroupId;
        } elseif ($attributeCode === 'position') {
            $categoryId = $this->coreRegistry->registry('current_category')
                ? $this->coreRegistry->registry('current_category')->getId()
                : $this->storeManager->getStore()->getRootCategoryId();
            return 'category_position_' . $categoryId;
        }
        return $attributeCode;
    }
}
