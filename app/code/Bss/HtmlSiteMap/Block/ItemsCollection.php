<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    BSS_HtmlSiteMap
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HtmlSiteMap\Block;

use Magento\Directory\Helper\Data;
use Magento\Store\Model\Group;

class ItemsCollection extends \Magento\Framework\View\Element\Template
{
    const MAX_PRODUCTS = 'bss_htmlsitemap/product/max_products';
    const SORT_PRODUCT = 'bss_htmlsitemap/product/sort_product';
    const ORDER_PRODUCT = 'bss_htmlsitemap/product/order_product';
    const PRODUCT_LIST_NUMBER = '1';
    const STORE_VIEW_LIST_NUMBER = '2';
    const ADDITIONAL_LIST_NUMBER = '3';
    const CATE_AND_CMS_NUMBER = '4';

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    public $categoryHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var $category
     */
    public $category;

    /**
     * @var \Magento\Store\Block\Switcher\Interceptor
     */
    public $interceptor;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var $helper
     */
    public $helper;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    public $categoryFlatConfig;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    public $pageFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    public $page;
    
    /**
     * @var bool
     */
    public $storeInUrl;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    public $postDataHelper;

    /**
     * ItemsCollection constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Block\Switcher\Interceptor $interceptor
     * @param \Bss\HtmlSiteMap\Helper\Data $helper
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Block\Switcher\Interceptor $interceptor,
        \Bss\HtmlSiteMap\Helper\Data $helper,
        \Magento\Cms\Model\Page $page,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        $this->pageFactory = $pageFactory;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->page = $page;
        $this->scopeConfig = $context->getScopeConfig();
        $this->helper = $helper;
        $this->interceptor = $interceptor;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryHelper = $categoryHelper;
        $this->storeManager = $context->getStoreManager();
        $this->postDataHelper = $postDataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return \Bss\HtmlSiteMap\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
    
    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }
    
    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }
    
    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->storeManager->getStore()->getCurrentUrl($fromStore);
    }
    
    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->storeManager->getStore()->isActive();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $maxProducts = $this->scopeConfig->getValue(
            self::MAX_PRODUCTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $maxProducts = (int)$maxProducts;
        if ($maxProducts >= 0 && $maxProducts != null) {
            if ($maxProducts > 50000) {
                $maxProducts = 50000;
            }
        } else {
            $maxProducts = 50000;
        }

        $sortProduct = $this->scopeConfig->getValue(
            self::SORT_PRODUCT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $orderProduct = $this->scopeConfig->getValue(
            self::ORDER_PRODUCT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $rulerStatus = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
        $collection->addAttributeToFilter('status', $rulerStatus);
        $collection->addWebsiteFilter();
        $collection->addFieldToFilter([['attribute'=>'visibility', 'neq'=>"1" ]]);
        $collection->addUrlRewrite();
        $collection->addAttributeToSort($sortProduct, $orderProduct);
        $collection->setPageSize($maxProducts); // fetching only 3 products
        return $collection;
    }
    
    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
                
        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }
        
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        
        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }
        
        return $collection;
    }

    /**
     * @return \Magento\Catalog\Helper\Category
     */
    public function getCategoryHelper()
    {
        return $this->categoryHelper;
    }

    /**
     * @return $this|\Magento\Cms\Model\Page
     */
    public function getCmsPages()
    {

        $this->getStoreId();
        $page = $this->pageFactory->create()->load(1);
        return $page;
    }

    /**
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    /**
     * @param object $category
     * @return array
     */
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }

    /**
     * @param object $category
     * @param bool $categoryDisable
     * @return string
     */
    public function getAllCategories($category, $categoryDisable)
    {
        $categoryHelper = $this->getCategoryHelper();
        $categoryHtmlEnd = null;
        if ($childrenCategories = $this->getChildCategories($category)) {
            foreach ($childrenCategories as $category) {
                if (!$category->getIsActive()) {
                    continue;
                }
                $categoryString = (string)$category->getId();
                $categoryString = ",".$categoryString.",";
                $categoryValidate = strpos($categoryDisable, $categoryString);
                if ($categoryValidate == false) {
                    $categoryUrl = $categoryHelper->getCategoryUrl($category);
                    $categoryHtml = '<li><a href="'.$categoryUrl.'">'.$category->getName().'</a></li>';
                    $categoryReturn = $this->getAllCategories($category, $categoryDisable);
                    $categoryHtml = $categoryHtml.$categoryReturn;
                } else {
                    $categoryHtml = null;
                }
                $categoryHtmlEnd = $categoryHtmlEnd.$categoryHtml;
            }
            return '<ul>'.$categoryHtmlEnd.'</ul>';
        }
    }

    /**
     * @return int|null|string
     */

    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return int|null|string
     */

    public function getCurrentGroupId()
    {
        return $this->_storeManager->getStore()->getGroupId();
    }

    /**
     * @return int
     */

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return array
     */

    public function getRawGroups()
    {
        if (!$this->hasData('raw_groups')) {
            $websiteGroups = $this->_storeManager->getWebsite()->getGroups();

            $groups = [];
            foreach ($websiteGroups as $group) {
                $groups[$group->getId()] = $group;
            }
            $this->setData('raw_groups', $groups);
        }
        return $this->getData('raw_groups');
    }

    /**
     * @return array
     */

    public function getRawStores()
    {
        if (!$this->hasData('raw_stores')) {
            $websiteStores = $this->_storeManager->getWebsite()->getStores();
            $stores = [];
            foreach ($websiteStores as $store) {
                if (!$store->isActive()) {
                    continue;
                }
                $localeCode = $this->_scopeConfig->getValue(
                    Data::XML_PATH_DEFAULT_LOCALE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                );
                $store->setLocaleCode($localeCode);
                $params = ['_query' => []];
                if (!$this->isStoreInUrl()) {
                    $params['_query']['___store'] = $store->getCode();
                }
                $baseUrl = $store->getUrl('', $params);

                $store->setHomeUrl($baseUrl);
                $stores[$store->getGroupId()][$store->getId()] = $store;
            }
            $this->setData('raw_stores', $stores);
        }
        return $this->getData('raw_stores');
    }

    /**
     * Retrieve list of store groups with default urls set
     *
     * @return Group[]
     */

    public function getGroups()
    {
        if (!$this->hasData('groups')) {
            $rawGroups = $this->getRawGroups();
            $rawStores = $this->getRawStores();

            $groups = [];
            $localeCode = $this->_scopeConfig->getValue(
                Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($rawGroups as $group) {
                if (!isset($rawStores[$group->getId()])) {
                    continue;
                }
                if ($group->getId() == $this->getCurrentGroupId()) {
                    $groups[] = $group;
                    continue;
                }

                $store = $group->getDefaultStoreByLocale($localeCode);

                if ($store) {
                    $group->setHomeUrl($store->getHomeUrl());
                    $groups[] = $group;
                }
            }
            $this->setData('groups', $groups);
        }
        return $this->getData('groups');
    }

    /**
     * @return \Magento\Store\Model\Store[]
     */

    public function getStores()
    {
        if (!$this->getData('stores')) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $stores = [];
            } else {
                $stores = $rawStores[$groupId];
            }
            $this->setData('stores', $stores);
        }
        return $this->getData('stores');
    }

    /**
     * @return string
     */
    public function getCurrentStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @return bool
     */
    public function isStoreInUrl()
    {
        if ($this->storeInUrl === null) {
            $this->storeInUrl = $this->_storeManager->getStore()->isUseStoreInUrl();
        }
        return $this->storeInUrl;
    }

    /**
     * Get store name
     *
     * @return null|string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Returns target store post data
     *
     * @param \Magento\Store\Model\Store $store
     * @param array $data
     * @return string
     */
    public function getTargetStorePostData(\Magento\Store\Model\Store $store, $data = [])
    {
        $data[\Magento\Store\Api\StoreResolverInterface::PARAM_NAME] = $store->getCode();
        return $this->postDataHelper->getPostData(
            $this->getUrl('/'),
            $data
        );
    }
}
