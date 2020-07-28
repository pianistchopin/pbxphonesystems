<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model;

class Label extends \Magento\Framework\Model\AbstractModel implements \Amasty\ShippingBar\Api\Data\LabelInterface
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(ResourceModel\Label::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelId()
    {
        return $this->_getData(self::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelId($labelId)
    {
        $this->setData(self::LABEL_ID, $labelId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileId()
    {
        return $this->_getData(self::PROFILE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileId($profileId)
    {
        $this->setData(self::PROFILE_ID, $profileId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->setData(self::ACTION, $action);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->_getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->setData(self::LABEL, $label);

        return $this;
    }
}
