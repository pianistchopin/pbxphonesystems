<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model\ResourceModel;

use Amasty\ShippingArea\Api\Data\AreaInterface;

class Area extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_shipping_area';
    const DELIMITER = ',';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, AreaInterface::AREA_ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\ShippingArea\Model\Area $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = $this->prepareOptionalFields($object->getData());

        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                if ($key == AreaInterface::POSTCODE_SET) {
                    $value = \Zend_Json::encode($value);
                } else {
                    $value = implode(self::DELIMITER, $value);
                }
            }
        }
        $object->setData($data);

        return parent::_beforeSave($object);
    }

    /**
     * @param array|null $data
     *
     * @return array
     */
    private function prepareOptionalFields($data)
    {
        if (!$data) {
            return [];
        }

        $data[AreaInterface::COUNTRY_SET] = $data[AreaInterface::COUNTRY_CONDITION]
            ? $data[AreaInterface::COUNTRY_SET] : null;
        $data[AreaInterface::CITY_SET] = $data[AreaInterface::CITY_CONDITION]
            ? $data[AreaInterface::CITY_SET] : null;
        $data[AreaInterface::POSTCODE_SET] = $data[AreaInterface::POSTCODE_CONDITION]
            ? $data[AreaInterface::POSTCODE_SET] : null;
        $data[AreaInterface::ADDRESS_SET] = $data[AreaInterface::ADDRESS_CONDITION]
            ? $data[AreaInterface::ADDRESS_SET] : null;

        if (!$data[AreaInterface::STATE_CONDITION]) {
            $data[AreaInterface::STATE_SET_LISTING] = $data[AreaInterface::STATE_SET] = null;
        } elseif ($data[AreaInterface::STATE_SET_LISTING]) {
            $data[AreaInterface::STATE_SET] = null;
        } else {
            $data[AreaInterface::STATE_SET_LISTING] = null;
        }

        return $data;
    }
}
