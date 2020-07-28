<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Helper;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Amasty\ShopbySeo\Model\Source\IndexMode;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Meta
 * @package Amasty\ShopbySeo\Helper
 */
class Meta extends AbstractHelper
{
    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $dataHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var boolean
     */
    private $isFollowingAllowed;

    /**
     * @var \Magento\Framework\View\LayoutInterface\Proxy
     */
    private $layout;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var Resolver
     */
    private $layerResolver;

    public function __construct(
        Context $context,
        \Amasty\Shopby\Helper\Data $dataHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        Resolver $layerResolver
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->layout = $layout;
        $this->registry = $registry;
        $this->request = $request;
        $this->layerResolver = $layerResolver;
    }

    /**
     * @param PageConfig $pageConfig
     * @return void
     */
    public function setPageTags(PageConfig $pageConfig)
    {
        $this->pageConfig = $pageConfig;
        if ($this->isModifyRobotsEnable()) {
            $this->setRobots();
        }
    }

    /**
     * @return void
     */
    private function setRobots()
    {
        $follow = $this->getFollowTagValue();
        $robots = $this->pageConfig->getRobots();
        $robots = $this->getIndexTagValue() ? $robots : preg_replace('/\w*index/i', 'noindex', $robots);
        $robots = $follow ? $robots : preg_replace('/\w*follow/i', 'nofollow', $robots);
        $this->isFollowingAllowed = $follow;
        $this->pageConfig->setRobots($robots);
    }

    /**
     * @return bool
     */
    public function getIndexTagValue()
    {
        $appliedFiltersSettings = $this->dataHelper->getSelectedFiltersSettings();
        $index = $this->getIndexTag($appliedFiltersSettings);
        foreach ($appliedFiltersSettings as $row) {
            if (!$index) {
                break;
            }

            $data = new DataObject([
                'setting' => $row['setting'],
                'filter' => $row['filter']
            ]);
            $index = $index ? $this->getIndexTagByData($index, $data) : $index;
        }

        return $index;
    }

    /**
     * @return bool
     */
    public function getFollowTagValue()
    {
        $appliedFiltersSettings = $this->dataHelper->getSelectedFiltersSettings();
        $follow = $this->getFollowTag();
        foreach ($appliedFiltersSettings as $row) {
            if (!$follow) {
                break;
            }

            $data = new DataObject([
                'setting' => $row['setting'],
                'filter' => $row['filter']
            ]);
            $follow = $follow ? $this->getFollowTagByData($follow, $data) : $follow;
        }

        return $follow;
    }

    /**
     * @param array[] $appliedFiltersSettings
     * @return bool
     */
    private function getIndexTag(array $appliedFiltersSettings)
    {
        $result = true;
        if ($this->request->getParam('p', 0) > 0) {
            $noIndexPagedCategory = $this->scopeConfig->getValue(
                'amasty_shopby_seo/robots/noindex_paginated',
                ScopeInterface::SCOPE_STORE
            );
            $result = !$noIndexPagedCategory;
        }

        if ($result) {
            $isNoIndexWithMultiple = $this->scopeConfig->getValue(
                'amasty_shopby_seo/robots/noindex_multiple',
                ScopeInterface::SCOPE_STORE
            );
            if ($isNoIndexWithMultiple && count($appliedFiltersSettings) > 1) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function getFollowTag()
    {
        return true;
    }

    /**
     * Enhanced in plugins.
     *
     * @param bool $indexTag
     * @param DataObject $data
     * @return bool
     */
    public function getIndexTagByData($indexTag, DataObject $data)
    {
        return $this->getTagByData(FilterSettingInterface::INDEX_MODE, $indexTag, $data);
    }

    /**
     * Enhanced in plugins.
     *
     * @param bool $followTag
     * @param DataObject $data
     * @return bool
     */
    public function getFollowTagByData($followTag, DataObject $data)
    {
        return $this->getTagByData(FilterSettingInterface::FOLLOW_MODE, $followTag, $data);
    }

    /**
     * @param $tagKey
     * @param $tagValue
     * @param $data
     * @return bool
     */
    public function getTagByData($tagKey, $tagValue, $data)
    {
        if ($this->isModifyRobotsEnable()) {
            /** @var FilterSettingInterface $setting */
            $setting = $data['setting'];
            /** @var FilterInterface $filter */
            $filter = $data['filter'];
            $value = $this->_getRequest()->getParam($filter->getRequestVar());
            $count = count(is_array($value) ? $value : explode(',', $value));
            $mode = $tagKey == FilterSettingInterface::INDEX_MODE
                ? $setting->getIndexMode()
                : $setting->getFollowMode();

            if ($mode == IndexMode::MODE_NEVER || ($mode == IndexMode::MODE_SINGLE_ONLY && $count > 1)) {
                $tagValue = false;
            }
        }

        return $tagValue;
    }

    /**
     * @return bool
     */
    public function isFollowingAllowed()
    {
        return $this->isFollowingAllowed;
    }

    /**
     * @return bool
     */
    public function isModifyRobotsEnable()
    {
        return $this->scopeConfig->isSetFlag('amasty_shopby_seo/robots/control_robots', ScopeInterface::SCOPE_STORE);
    }
}
