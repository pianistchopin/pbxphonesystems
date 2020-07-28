<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Layer\Filter;

use Amasty\Shopby;
use Amasty\ShopbySeo\Model\FollowResolver;
use Magento\Store\Model\Store;

/**
 * Class Item
 * @package Amasty\Shopby\Model\Layer\Filter
 */
class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var  Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    /**
     * @var  Shopby\Helper\UrlBuilder
     */
    protected $urlBuilderHelper;

    /**
     * @var FollowResolver
     */
    protected $followResolver;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    private $groupHelper;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\Request\Http $request,
        Shopby\Helper\FilterSetting $filterSettingHelper,
        Shopby\Helper\UrlBuilder $urlBuilderHelper,
        FollowResolver $followResolver,
        \Amasty\Shopby\Helper\Group $groupHelper,
        array $data = []
    ) {
        $this->_request = $request;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->urlBuilderHelper = $urlBuilderHelper;
        $this->followResolver = $followResolver;
        $this->groupHelper = $groupHelper;
        parent::__construct($url, $htmlPagerBlock, $data);
    }
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->urlBuilderHelper->buildUrl($this->getFilter(), $this->getValue());
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl($value = null)
    {
        $value = $value !== null ? $value : $this->getValue();

        return $this->urlBuilderHelper->buildUrl($this->getFilter(), $value);
    }

    /**
     * @return bool
     */
    public function getRelNofollow()
    {
        return !$this->followResolver->relFollow($this);
    }

    /**
     * @return string
     */
    public function getOptionLabel()
    {
        return $this->groupHelper->chooseGroupLabel($this->getLabel());
    }
}
