<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model\ResourceModel;

use Amasty\Shiprestriction\Model\ConstantsInterface;

/**
 * Class Rule
 */
class Rule extends \Amasty\CommonRules\Model\ResourceModel\AbstractRule
{
    const TABLE_NAME = 'amasty_shiprestriction_rule';
    const ATTRIBUTE_TABLE_NAME = 'amasty_shiprestriction_attribute';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'rule_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            // convert data from array to string
            $value = $object->getData($field);
            $object->setData($field, '');

            if (is_array($value)) {
                if ($field == 'methods') {
                    $carriers = [];

                    foreach ($value as $key => $shipMethod) {
                        if (strpos($shipMethod, '_') === false) {
                            $carriers[] = $shipMethod;
                            unset($value[$key]);
                        }
                    }
                    $object->setCarriers(implode(',', $carriers));
                }

                $object->setData($field, implode(',', $value));
            }
        }

        return parent::_beforeSave($object);
    }
}
