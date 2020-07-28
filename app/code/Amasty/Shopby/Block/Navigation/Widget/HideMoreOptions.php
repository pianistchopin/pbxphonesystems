<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation\Widget;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\Shopby\Model\Source\DisplayMode;

/**
 * Class HideMoreOptions
 * @package Amasty\Shopby\Block\Navigation\Widget
 */
class HideMoreOptions extends \Magento\Framework\View\Element\Template implements WidgetInterface
{
    /**
     * @var FilterSettingInterface
     */
    private $filterSetting;

    /**
     * @var string
     */
    protected $_template = 'layer/widget/hide_more_options.phtml';

    /**
     * @param FilterSettingInterface $filterSetting
     * @return $this
     */
    public function setFilterSetting(FilterSettingInterface $filterSetting)
    {
        $this->filterSetting = $filterSetting;
        return $this;
    }

    /**
     * @return FilterSettingInterface
     */
    public function getFilterSetting()
    {
        return $this->filterSetting;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $forbidenDisplayModes = [DisplayMode::MODE_DROPDOWN, DisplayMode::MODE_FROM_TO_ONLY, DisplayMode::MODE_SLIDER];
        $isShowInState = $this->getIsState() && $this->getUnfoldedOptions();
        $isShowInFilter = $this->filterSetting
            && !in_array($this->filterSetting->getDisplayMode(), $forbidenDisplayModes)
            && $this->filterSetting->getNumberUnfoldedOptions();

        return $isShowInState || $isShowInFilter
            ? parent::toHtml()
            : '';
    }
}
