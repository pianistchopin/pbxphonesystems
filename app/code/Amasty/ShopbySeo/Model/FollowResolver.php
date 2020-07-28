<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Model;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\Shopby\Model\Layer\Filter\Item;
use Amasty\ShopbyBase\Helper\FilterSetting as FilterHelper;
use Amasty\ShopbySeo\Helper\Meta;
use Amasty\ShopbySeo\Model\Source\IndexMode;
use Amasty\ShopbySeo\Model\Source\RelNofollow;
use Magento\Framework\App\Helper\Context;

/**
 * Class FollowResolver
 * @package Amasty\ShopbySeo\Model
 */
class FollowResolver
{
    /**
     * @var FilterHelper
     */
    private $filterHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Meta
     */
    private $metaHelper;

    /**
     * @var \Amasty\ShopbyBase\Helper\Data
     */
    private $baseHelper;

    public function __construct(
        Context $context,
        FilterHelper $filterHelper,
        Meta $meta,
        \Amasty\ShopbyBase\Helper\Data $baseHelper
    ) {
        $this->filterHelper = $filterHelper;
        $this->request = $context->getRequest();
        $this->metaHelper = $meta;
        $this->baseHelper = $baseHelper;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function relFollow(Item $item)
    {
        if (!$this->baseHelper->isEnableRelNofollow() || $this->metaHelper->isFollowingAllowed() === false) {
            return true;
        }

        $filter = $item->getFilter();
        $setting = $this->filterHelper->getSettingByLayerFilter($filter);
        if (!$setting || ($setting->getId() && $setting->getRelNofollow() != RelNofollow::MODE_AUTO)) {
            // Bypass unknown filter
            // if nofollow enabled, need to set it
            return false;
        }

        $value = $item->getValueString();
        $currentValue = $this->request->getParam($filter->getRequestVar());
        $currentValue = $currentValue ? explode(',', $currentValue) : [];

        $deltaDeep = in_array($value, $currentValue) ? -1 : 1;
        $deltaDeep = !$setting->isMultiselect() ? $deltaDeep - 1 : $deltaDeep;
        $targetDeep = count($currentValue) + $deltaDeep;

        if ($targetDeep == 0) {
            return true;
        }

        $allowedDeep = $this->getAllowedFilterDeep($setting);
        return $targetDeep <= $allowedDeep;
    }

    /**
     * @param FilterSettingInterface $filterSetting
     * @return mixed
     */
    protected function getAllowedFilterDeep(FilterSettingInterface $filterSetting)
    {
        $deepByMode = $this->getDeepByMode();
        $indexDeep = $deepByMode[$filterSetting->getIndexMode()];
        $followDeep = $deepByMode[$filterSetting->getFollowMode()];

        return max($indexDeep, $followDeep);
    }

    /**
     * @return array
     */
    protected function getDeepByMode()
    {
        return [
            IndexMode::MODE_NEVER => 0,
            IndexMode::MODE_SINGLE_ONLY => 1,
            IndexMode::MODE_ALWAYS => 2,
        ];
    }
}
