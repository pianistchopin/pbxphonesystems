<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model;

use Amasty\ShippingArea\Api\Data\AreaInterface;
use Amasty\ShippingArea\Model\ResourceModel\Area as AreaResource;
use Magento\Framework\Model\AbstractModel;

/**
 * @method AreaResource getResource()
 * @method \Amasty\ShippingArea\Model\ResourceModel\Area\Collection getCollection()
 */
class Area extends AbstractModel implements AreaInterface
{
    const STREET_ARRAY_KEY = 'street_arr';
    protected $_eventPrefix = 'amasty_shippingarea_area';

    protected function _construct()
    {
        $this->_init(AreaResource::class);
    }

    /**
     * @return array
     */
    public function getStreetArray()
    {
        if (!$this->hasData(self::STREET_ARRAY_KEY)) {
            $street = (string)$this->getAddressSet();
            $street = str_replace(["\r\n", "\r"], "\n", $street);
            $this->setData(self::STREET_ARRAY_KEY, explode("\n", $street));
        }

        return $this->_getData(self::STREET_ARRAY_KEY);
    }

    /**
     * @inheritdoc
     */
    public function getCountrySet()
    {
        $data = $this->_getData(AreaInterface::COUNTRY_SET);

        if (is_string($data)) {
            $data = explode(AreaResource::DELIMITER, $data);
            $this->setCountrySet($data);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getPostcodeSet()
    {
        $postcodeSet = $this->_getData(AreaInterface::POSTCODE_SET);
        if (is_string($postcodeSet) && !empty($postcodeSet)) {
            $postcodeSet = \Zend_Json::decode($postcodeSet);
            $this->setPostcodeSet($postcodeSet);
        }

        return $postcodeSet;
    }

    /**
     * @inheritdoc
     */
    public function getStateSetListing()
    {
        $stateSetListing = $this->_getData(AreaInterface::STATE_SET_LISTING);

        if (is_string($stateSetListing) && !empty($stateSetListing)) {
            $stateSetListing = explode(AreaResource::DELIMITER, $stateSetListing);
            $this->setStateSetListing($stateSetListing);
        }

        return $stateSetListing;
    }

    /**
     * @inheritdoc
     */
    public function getAreaId()
    {
        return $this->_getData(AreaInterface::AREA_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAreaId($areaId)
    {
        $this->setData(AreaInterface::AREA_ID, $areaId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(AreaInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(AreaInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->_getData(AreaInterface::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->setData(AreaInterface::DESCRIPTION, $description);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsEnabled()
    {
        return $this->_getData(AreaInterface::IS_ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function setIsEnabled($isEnabled)
    {
        $this->setData(AreaInterface::IS_ENABLED, $isEnabled);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountryCondition()
    {
        return $this->_getData(AreaInterface::COUNTRY_CONDITION);
    }

    /**
     * @inheritdoc
     */
    public function setCountryCondition($countryCondition)
    {
        $this->setData(AreaInterface::COUNTRY_CONDITION, $countryCondition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCountrySet($countrySet)
    {
        $this->setData(AreaInterface::COUNTRY_SET, $countrySet);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStateCondition()
    {
        return $this->_getData(AreaInterface::STATE_CONDITION);
    }

    /**
     * @inheritdoc
     */
    public function setStateCondition($stateCondition)
    {
        $this->setData(AreaInterface::STATE_CONDITION, $stateCondition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setStateSetListing($stateSetListing)
    {
        $this->setData(AreaInterface::STATE_SET_LISTING, $stateSetListing);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStateSet()
    {
        return $this->_getData(AreaInterface::STATE_SET);
    }

    /**
     * @inheritdoc
     */
    public function setStateSet($stateSet)
    {
        $this->setData(AreaInterface::STATE_SET, $stateSet);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCityCondition()
    {
        return $this->_getData(AreaInterface::CITY_CONDITION);
    }

    /**
     * @inheritdoc
     */
    public function setCityCondition($cityCondition)
    {
        $this->setData(AreaInterface::CITY_CONDITION, $cityCondition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCitySet()
    {
        return $this->_getData(AreaInterface::CITY_SET);
    }

    /**
     * @inheritdoc
     */
    public function setCitySet($citySet)
    {
        $this->setData(AreaInterface::CITY_SET, $citySet);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPostcodeCondition()
    {
        return $this->_getData(AreaInterface::POSTCODE_CONDITION);
    }

    /**
     * @inheritdoc
     */
    public function setPostcodeCondition($postcodeCondition)
    {
        $this->setData(AreaInterface::POSTCODE_CONDITION, $postcodeCondition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPostcodeSet($postcodeSet)
    {
        $this->setData(AreaInterface::POSTCODE_SET, $postcodeSet);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddressCondition()
    {
        return $this->_getData(AreaInterface::ADDRESS_CONDITION);
    }

    /**
     * @inheritdoc
     */
    public function setAddressCondition($addressCondition)
    {
        $this->setData(AreaInterface::ADDRESS_CONDITION, $addressCondition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddressSet()
    {
        return $this->_getData(AreaInterface::ADDRESS_SET);
    }

    /**
     * @inheritdoc
     */
    public function setAddressSet($addressSet)
    {
        $this->setData(AreaInterface::ADDRESS_SET, $addressSet);

        return $this;
    }
}
