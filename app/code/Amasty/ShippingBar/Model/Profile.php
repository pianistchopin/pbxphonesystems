<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model;

use Amasty\ShippingBar\Api\Data\ProfileInterface;

class Profile extends \Magento\Framework\Model\AbstractModel implements ProfileInterface
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(ResourceModel\Profile::class);
    }

    public function getDataForForm()
    {
        $data = parent::getData();

        if (!isset($data['labels'])) {
            return $data;
        }
        $result = [];

        foreach ($data['labels'] as $action => $stores) {
            foreach ($stores as $storeId => $label) {
                $result['labels[' . $action . '][' . $storeId . ']'] = $label;
            }
        }

        unset($data['labels']);
        $data = array_merge($data, $result);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getProfileId()
    {
        return $this->_getData(ProfileInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setProfileId($profileId)
    {
        $this->setData(ProfileInterface::ID, $profileId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(ProfileInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(ProfileInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(ProfileInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(ProfileInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGoal()
    {
        return $this->_getData(ProfileInterface::GOAL);
    }

    /**
     * @inheritdoc
     */
    public function setGoal($goal)
    {
        $this->setData(ProfileInterface::GOAL, $goal);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGoalSource()
    {
        return $this->_getData(ProfileInterface::GOAL_SOURCE);
    }

    /**
     * @inheritdoc
     */
    public function setGoalSource($sourceId)
    {
        $this->setData(ProfileInterface::GOAL_SOURCE, $sourceId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->_getData(ProfileInterface::PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->setData(ProfileInterface::PRIORITY, $priority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStores()
    {
        return $this->_getData(ProfileInterface::STORES);
    }

    /**
     * @inheritdoc
     */
    public function setStores($stores)
    {
        $this->setData(ProfileInterface::STORES, $stores);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroups()
    {
        return $this->_getData(ProfileInterface::CUSTOMER_GROUPS);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroups($customerGroups)
    {
        $this->setData(ProfileInterface::CUSTOMER_GROUPS, $customerGroups);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->_getData(ProfileInterface::POSITION);
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->setData(ProfileInterface::POSITION, $position);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPages()
    {
        return $this->_getData(ProfileInterface::PAGES);
    }

    /**
     * @inheritdoc
     */
    public function setPages($pages)
    {
        $this->setData(ProfileInterface::PAGES, $pages);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getActionClickable()
    {
        return $this->_getData(ProfileInterface::ACTION_CLICKABLE);
    }

    /**
     * @inheritdoc
     */
    public function setActionClickable($actionClickable)
    {
        $this->setData(ProfileInterface::ACTION_CLICKABLE, $actionClickable);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getActionLink()
    {
        return $this->_getData(ProfileInterface::ACTION_LINK) ?: '';
    }

    /**
     * @inheritdoc
     */
    public function setActionLink($actionLink)
    {
        $this->setData(ProfileInterface::ACTION_LINK, $actionLink);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCloseable()
    {
        return $this->_getData(ProfileInterface::CLOSEABLE);
    }

    /**
     * @inheritdoc
     */
    public function setCloseable($closeable)
    {
        $this->setData(ProfileInterface::CLOSEABLE, $closeable);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCarIconVisible()
    {
        return $this->_getData(ProfileInterface::CAR_ICON_VISIBLE);
    }

    /**
     * @inheritdoc
     */
    public function setCarIconVisible($isCarIconVisible)
    {
        $this->setData(ProfileInterface::CAR_ICON_VISIBLE, $isCarIconVisible);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTextFont()
    {
        return $this->_getData(ProfileInterface::TEXT_FONT);
    }

    /**
     * @inheritdoc
     */
    public function setTextFont($textFont)
    {
        $this->setData(ProfileInterface::TEXT_FONT, $textFont);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTextSize()
    {
        return $this->_getData(ProfileInterface::TEXT_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setTextSize($textSize)
    {
        $this->setData(ProfileInterface::TEXT_SIZE, $textSize);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTextColor()
    {
        return $this->_getData(ProfileInterface::TEXT_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setTextColor($textColor)
    {
        $this->setData(ProfileInterface::TEXT_COLOR, $textColor);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtraColor()
    {
        return $this->_getData(ProfileInterface::EXTRA_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setExtraColor($extraColor)
    {
        $this->setData(ProfileInterface::EXTRA_COLOR, $extraColor);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBackgroundColor()
    {
        return $this->_getData(ProfileInterface::BACKGROUND_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->setData(ProfileInterface::BACKGROUND_COLOR, $backgroundColor);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomStyle()
    {
        return $this->_getData(ProfileInterface::CUSTOM_STYLE) ?: '';
    }

    /**
     * @inheritdoc
     */
    public function setCustomStyle($customStyle)
    {
        $this->setData(ProfileInterface::CUSTOM_STYLE, $customStyle);

        return $this;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->_getData('labels');
    }

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels(array $labels)
    {
        $this->setData('labels', $labels);

        return $this;
    }
}
