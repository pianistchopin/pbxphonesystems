<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyPage
 */


namespace Amasty\ShopbyPage\Model\Customizer\Category;

use Amasty\Shopby\Helper\Data as ShopbyHelper;
use Amasty\ShopbyBase\Model\Customizer\Category\CustomizerInterface;
use \Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Config as CatalogConfig;
use Amasty\ShopbyPage\Api\PageRepositoryInterface;
use Amasty\ShopbyPage\Api\Data\PageInterface;
use Amasty\ShopbyPage\Model\Page as PageEntity;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Page
 *
 * @package Amasty\ShopbyPage\Model\Customizer\Category
 */
class Page implements CustomizerInterface
{
    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $amshopbyRequest;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ShopbyHelper
     */
    private $shopbyHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        CatalogConfig $catalogConfig,
        Registry $registry,
        \Amasty\Shopby\Model\Request $amshopbyRequest,
        ScopeConfigInterface $scopeConfig,
        ShopbyHelper $shopbyHelper
    ) {
        $this->pageRepository = $pageRepository;
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
        $this->amshopbyRequest = $amshopbyRequest;
        $this->shopbyHelper = $shopbyHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareData(\Magento\Catalog\Model\Category $category)
    {
        $searchResults = $this->pageRepository->getList($category->getId(), $category->getStoreId());

        if ($searchResults->getTotalCount() > 0) {
            foreach ($searchResults->getItems() as $pageData) {
                if (!$this->matchCurrentFilters($pageData)) {
                    return null;
                }
            }
            $this->modifyCategory($pageData, $category);
            $this->registry->register(PageEntity::MATCHED_PAGE, $pageData);
        }

        return null;
    }

    /**
     * Compare page filters with selected filters
     *
     * @param PageInterface $pageData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function matchCurrentFilters(PageInterface $pageData)
    {
        $isMatched = true;
        $conditions = $pageData->getConditions();

        foreach ($conditions as $condition) {
            if (!isset($condition['filter'])) {
                $isMatched = false;
                break;
            }

            if (!isset($condition['value'])
                || $this->isConditionNotMatched($condition['filter'], $condition['value'])
            ) {
                $isMatched = false;
                break;
            }
        }

        if ($isMatched
            && $this->isStrictModeEnabled()
            && !$this->checkStrictMatch($conditions)
        ) {
            $isMatched = false;
        }

        return $isMatched;
    }

    /**
     * @return bool
     */
    private function isStrictModeEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby_page/general/page_match_strict',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $attributeCode
     * @param string $attributeValue
     *
     * @return bool
     */
    private function isConditionNotMatched($attributeCode, $attributeValue)
    {
        $result = false;
        $attribute = $this->catalogConfig->getAttribute(Product::ENTITY, $attributeCode);
        if ($attribute->getId()) {
            $paramValue = $this->amshopbyRequest->getParam($attribute->getAttributeCode());

            //compare with array for multiselect attributes
            if ($attribute->getFrontendInput() === 'multiselect') {
                $result = $this->checkMultiselectAttribute($paramValue, $attributeValue);
            } else {
                $result = !$this->checkSingleSelectAttribute($paramValue, $attributeValue);
            }
        }

        return $result;
    }

    /**
     * @param string $currentValue
     * @param string|array $expectedValue
     *
     * @return bool
     */
    private function checkMultiselectAttribute($currentValue, $expectedValue)
    {
        $result = false;
        $currentValue = explode(',', $currentValue);

        if (!is_array($expectedValue) || array_diff($expectedValue, $currentValue)) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param string $currentValue
     * @param string $expectedValue
     *
     * @return bool
     */
    private function checkSingleSelectAttribute($currentValue, $expectedValue)
    {
        $result = $currentValue == $expectedValue;
        if (!$result && strpos($currentValue, ',') !== false) {
            $currentValue = explode(',', $currentValue);
            $result = in_array($expectedValue, $currentValue);
        }
        
        return $result;
    }

    /**
     * @param $conditions
     * @return bool
     */
    private function checkStrictMatch($conditions)
    {
        $strict = true;
        $appliedFilters = $this->shopbyHelper->getSelectedFiltersSettings();
        $conditions = $this->findSameConditionsAndConvert($conditions);
        if (count($appliedFilters) != count($conditions)) {
            $strict = false;
        } else {
            foreach ($appliedFilters as $item) {
                /** @var AbstractFilter $filter */
                $filter = $item['filter'];
                if (!$filter->hasData('attribute_model') ||
                    !$this->matchAppliedFilter($filter, $conditions)
                ) {
                    $strict = false;
                    break;
                }
            }
        }

        return $strict;
    }

    /**
     * @param array $conditions
     *
     * @return array
     */
    private function findSameConditionsAndConvert($conditions)
    {
        $tmp = [];
        foreach ($conditions as $condition) {
            if (isset($condition['filter']) && isset($condition['value'])) {
                $key = $condition['filter'];
                if (isset($tmp[$key])) {
                    $tmp[$key]['value'] .= ',' .  $condition['value'];
                } else {
                    $tmp[$key] = $condition;
                }
            }
        }

        return array_values($tmp);
    }

    /**
     * @param AbstractFilter $filter
     * @param array $conditions
     *
     * @return bool
     */
    private function matchAppliedFilter($filter, $conditions)
    {
        $result = true;
        $attribute = $filter->getAttributeModel();
        $paramValue = $this->amshopbyRequest->getParam($filter->getRequestVar());
        foreach ($conditions as $condition) {
            if ($condition['filter'] == $attribute->getAttributeId()) {
                if ($attribute->getFrontendInput() === 'multiselect') {
                    if (!isset($condition['value'])
                        || $this->checkMultiselectAttribute($paramValue, $condition['value'])
                    ) {
                        $result = false;
                        break;
                    }
                } elseif (!$this->checkSingleSelectAttribute($paramValue, $condition['value'])) {
                    $result = false;
                    break;
                }
                $result = true;
                break;
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param PageInterface|PageEntity $page
     * @param $pageValue
     * @param $categoryValue
     * @param null $delimiter
     * @return string
     */
    private function getModifiedCategoryData(
        PageInterface $page,
        $pageValue,
        $categoryValue,
        $delimiter = null
    ) {
        if ($delimiter !== null && $page->getPosition() !== PageEntity::POSITION_REPLACE) {
            $categoryValue = $this->insertIntoPosition($page->getPosition(), $pageValue, $categoryValue, $delimiter);
        } else {
            $categoryValue = $pageValue;
        }

        return $categoryValue;
    }

    /**
     * @param string $position
     * @param $pageValue
     * @param $categoryValue
     * @param $delimiter
     *
     * @return string
     */
    private function insertIntoPosition(
        $position,
        $pageValue,
        $categoryValue,
        $delimiter
    ) {
        //if has a delimiter, place at the start or end
        $categoryValueArr = explode($delimiter, $categoryValue);

        if ($position === PageEntity::POSITION_AFTER) {
            $categoryValueArr[] = $pageValue;
        } else {
            $categoryValueArr = array_merge([$pageValue], $categoryValueArr);
        }

        $categoryValue = implode($delimiter, $categoryValueArr);

        return $categoryValue;
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     * @param $pageKey
     * @param $categoryKey
     * @param null $delimiter
     */
    private function modifyCategoryData(
        PageInterface $page,
        CategoryInterface $category,
        $pageKey,
        $categoryKey,
        $delimiter = null
    ) {
        $categoryValue = $category->getData($categoryKey);
        $pageValue = $page->getData($pageKey);
        $modifiedData = $this->getModifiedCategoryData($page, $pageValue, $categoryValue, $delimiter);
        if ($modifiedData) {
            $category->setData($categoryKey, $modifiedData);
        }
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     */
    private function modifyCategory(PageInterface $page, CategoryInterface $category)
    {
        $categoryName = $this->getModifiedCategoryData($page, $page->getTitle(), $category->getName(), ' ');
        $category->setName($categoryName);

        $this->modifyCategoryData($page, $category, 'description', 'description', '<br>');
        $this->modifyCategoryData($page, $category, 'meta_title', 'meta_title', ' ');
        $this->modifyCategoryData($page, $category, 'meta_description', 'meta_description', ' ');
        $this->modifyCategoryData($page, $category, 'meta_keywords', 'meta_keywords', ',');
        $this->modifyCategoryData($page, $category, 'top_block_id', 'landing_page');
        $this->modifyCategoryData($page, $category, 'bottom_block_id', 'bottom_cms_block');
        $this->modifyCategoryData($page, $category, 'url', 'url');

        if ($page->getImage()) {
            $category->setData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL, $page->getImageUrl());
        }

        if ($page->getTopBlockId()) {
            $category->setData(CategoryManager::CATEGORY_FORCE_MIXED_MODE, 1);
        }

        if ($page->getUrl()) {
            $category->setData(PageEntity::CATEGORY_FORCE_USE_CANONICAL, 1);
        }
    }
}
