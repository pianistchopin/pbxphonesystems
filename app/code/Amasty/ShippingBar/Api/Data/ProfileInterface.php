<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Api\Data;

interface ProfileInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const GOAL_SOURCE = 'goal_source';
    const GOAL = 'goal';
    const STORES = 'stores';
    const CUSTOMER_GROUPS = 'customer_groups';
    const POSITION = 'position';
    const PAGES = 'pages';
    const ACTION_CLICKABLE = 'action_clickable';
    const ACTION_LINK = 'action_link';
    const CLOSEABLE = 'closeable';
    const CAR_ICON_VISIBLE = 'car_icon_visible';
    const TEXT_FONT = 'text_font';
    const TEXT_SIZE = 'text_size';
    const TEXT_COLOR = 'text_color';
    const EXTRA_COLOR = 'extra_color';
    const BACKGROUND_COLOR = 'background_color';
    const CUSTOM_STYLE = 'custom_style';
    const PRIORITY = 'priority';
    /**#@-*/

    /**
     * Key for data persistor
     */
    const FORM_NAMESPACE = 'amasty_shipbar_profile_form';

    /**
     * @return int
     */
    public function getProfileId();

    /**
     * @param int $profileId
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setProfileId($profileId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setStatus($status);

    /**
     * @return float
     */
    public function getGoal();

    /**
     * @param float $goal
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setGoal($goal);

    /**
     * @return int
     */
    public function getGoalSource();

    /**
     * @param int $sourceId
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setGoalSource($sourceId);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setPriority($priority);

    /**
     * @return string|null
     */
    public function getStores();

    /**
     * @param string|null $stores
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setStores($stores);

    /**
     * @return string|null
     */
    public function getCustomerGroups();

    /**
     * @param string|null $customerGroups
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setCustomerGroups($customerGroups);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getPages();

    /**
     * @param string $pages
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setPages($pages);

    /**
     * @return int
     */
    public function getActionClickable();

    /**
     * @param int $actionClickable
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setActionClickable($actionClickable);

    /**
     * @return string|null
     */
    public function getActionLink();

    /**
     * @param string|null $actionLink
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setActionLink($actionLink);

    /**
     * @return int
     */
    public function getCloseable();

    /**
     * @param int $closeable
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setCloseable($closeable);

    /**
     * @return int
     */
    public function getCarIconVisible();

    /**
     * @param bool $isCarIconVisible
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setCarIconVisible($isCarIconVisible);

    /**
     * @return int
     */
    public function getTextFont();

    /**
     * @param int $textFont
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setTextFont($textFont);

    /**
     * @return int
     */
    public function getTextSize();

    /**
     * @param int $textSize
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setTextSize($textSize);

    /**
     * @return string
     */
    public function getTextColor();

    /**
     * @param string $textColor
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setTextColor($textColor);

    /**
     * @return string
     */
    public function getExtraColor();

    /**
     * @param string $extraColor
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setExtraColor($extraColor);

    /**
     * @return string|null
     */
    public function getBackgroundColor();

    /**
     * @param string|null $backgroundColor
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setBackgroundColor($backgroundColor);

    /**
     * @return string|null
     */
    public function getCustomStyle();

    /**
     * @param string|null $customStyle
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function setCustomStyle($customStyle);
}
