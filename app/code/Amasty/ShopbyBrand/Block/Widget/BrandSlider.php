<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Block\Widget;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Registry;
use \Magento\Eav\Model\Entity\Attribute\Option;
use Amasty\ShopbyBrand\Helper\Data as DataHelper;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as OptionSettingCollectionFactory;

/**
 * Class BrandSlider
 *
 * @package Amasty\ShopbyBrand\Block\Widget
 */
class BrandSlider extends BrandListAbstract implements \Magento\Widget\Block\BlockInterface
{
    const HTML_ID = 'amslider_id';
    const DEFAULT_IMG_WIDTH = 130;
    const CONFIG_VALUES_PATH = 'amshopby_brand/slider';
    const PATH_SLIDER_COLOR_HEADER = 'amshopby_brand/slider/slider_header_color';
    const DEFAULT_VALUE_HEADER_COLOR = '#F58C12';
    const PATH_SLIDER_COLOR_TITLE = 'amshopby_brand/slider/slider_title_color';
    const DEFAULT_VALUE_TITLE_COLOR = '#FFFFFF';
    const PATH_SLIDER_TITLE = 'amshopby_brand/slider/slider_title';

    /** @var  Registry */
    protected $registry;

    /** @var int */
    protected $id;

    public function __construct(
        Context $context,
        Repository $repository,
        \Amasty\ShopbyBase\Model\OptionSettingFactory $optionSettingFactory,
        OptionSettingCollectionFactory $optionSettingCollectionFactory,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        Registry $registry,
        DataHelper $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Amasty\ShopbyBase\Api\UrlBuilderInterface $amUrlBuilder,
        \Amasty\ShopbyBrand\Model\ProductCount $productCount,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $repository,
            $optionSettingFactory,
            $optionSettingCollectionFactory,
            $collectionProvider,
            $productUrl,
            $categoryRepository,
            $helper,
            $messageManager,
            $amUrlBuilder,
            $productCount,
            $data
        );
        $this->registry = $registry;
        $this->getItems();
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @param OptionSettingInterface $setting
     * @return array
     */
    protected function getItemData(Option $option, OptionSettingInterface $setting)
    {
        $count = $this->_getOptionProductCount($setting->getValue());
        if ((!$this->helper->isDisplayZero() && !$count) || !$setting->getIsFeatured()) {
            $result = [];
        } else {
            $result = [
                'label' => $setting->getLabel() ?: $option->getLabel(),
                'url' => $this->getBrandUrl($option),
                'img' => $setting->getSliderImageUrl(),
                'position' => $setting->getSliderPosition(),
                'alt' => $setting->getSmallImageAlt() ?: $setting->getLabel()
            ];
        }

        return $result;
    }

    /**
     * @return $this
     */
    protected function applySorting()
    {
        if ($this->getData('sort_by') == 'name') {
            usort($this->items, [$this, '_sortByName']);
        } else {
            usort($this->items, [$this, '_sortByPosition']);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSliderOptions()
    {
        $options = [];
        $itemsPerView               = max(1, $this->getData('items_number'));
        $options['slidesPerView']   = $itemsPerView;
        $options['loop']            = $this->getData('infinity_loop')  ? 'true' : 'false';
        $options['simulateTouch']   = $this->getData('simulate_touch') ? 'true' : 'false';
        if ($this->getData('pagination_show')) {
            $options['pagination']  = '".swiper-pagination"';
            $options['paginationClickable'] = $this->getData('pagination_clickable') ? 'true' : 'false';
        }
        if ($this->getData('autoplay')) {
            $options['autoplay'] = (int)$this->getData('autoplay_delay');
        }
        return $options;
    }

    /**
     * Get html id attribute for slider in a case there are several sliders on the page.
     * @return string
     */
    public function getSliderId()
    {
        if ($this->id) {
            return $this->id;
        }
        $sliderId = (int)$this->registry->registry(self::HTML_ID);
        $sliderId++;
        $this->registry->unregister(self::HTML_ID);
        $this->registry->register(self::HTML_ID, $sliderId);
        $this->id = self::HTML_ID . $sliderId;
        return $this->id;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!count($this->getItems())) {
            return '';
        }
        return parent::toHtml();
    }
    
    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortByPosition($a, $b)
    {
        return $a['position'] - $b['position'];
    }

    /**
     * Getting slider header color
     *
     * @return string
     */
    public function getHeaderColor()
    {
        $res = $this->_scopeConfig
            ->getValue(self::PATH_SLIDER_COLOR_HEADER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $res ? $res : self::DEFAULT_VALUE_HEADER_COLOR;
    }

    /**
     * Getting slider title color
     *
     * @return string
     */
    public function getTitleColor()
    {
        $res = $this->_scopeConfig
            ->getValue(self::PATH_SLIDER_COLOR_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $res ? $res : self::DEFAULT_VALUE_TITLE_COLOR;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = $this->_scopeConfig
            ->getValue(self::PATH_SLIDER_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->escapeHtml($title);
    }

    /**
     * @return string
     */
    protected function getConfigValuesPath()
    {
        return self::CONFIG_VALUES_PATH;
    }

    /**
     * @return bool
     */
    public function isSliderEnabled()
    {
        return count($this->getItems()) > (int)$this->getData('items_number');
    }
}
