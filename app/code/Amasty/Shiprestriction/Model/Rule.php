<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model;

/**
 * Class Rule
 */
class Rule extends \Amasty\CommonRules\Model\Rule
{
    /**
     * _construct
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
        parent::_construct();
        $this->subtotalModifier->setSectionConfig(ConstantsInterface::SECTION_KEY);
    }

    /**
     * @return Rule
     */
    public function prepareForEdit()
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            $value = $this->getData($field);

            if (!is_array($value)) {
                $this->setData($field, explode(',', $value));
            }
        }

        $value = $this->getCarriers();

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (is_array($value)) {
            $this->setMethods(array_merge($value, $this->getMethods()));
        }

        return $this;
    }

    /**
     * @return array|null
     */
    public function getStores()
    {
        $stores = $this->_getData('stores');
        if (is_string($stores)) {
            $stores = explode(',', $stores);
        }

        return $stores;
    }

    /**
     * @param array|string $stores
     *
     * @return $this
     */
    public function setStores($stores)
    {
        if (is_array($stores)) {
            $stores = implode(',', $stores);
        }

        return $this->setData('stores', $stores);
    }
}
