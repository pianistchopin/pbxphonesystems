<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Model\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Amasty\Stockstatus\Model\Attribute\Creator;
use Magento\Eav\Model\Entity\Attribute\Source\Table as SourceTable;

class UpdaterAttribute extends ConfigValue
{
    const EXPECTED_DATE_CODE = 'stock_expected_date';

    const QTY_RULE_CODE = 'custom_stock_status_qty_rule';

    /**
     * @var array
     */
    private $attributesForUpdate = [
        'expected_date_enabled' => [
            'code' => self::EXPECTED_DATE_CODE,
            'label' => 'Expected Date',
            'args' => [
                'type' => 'datetime',
                'input' => 'date'
            ]
        ],
        'use_range_rules' => [
            'code' => self::QTY_RULE_CODE,
            'label' => 'Custom Stock Status Range Product Group',
            'args' => [
                'source' => SourceTable::class,
                'note' => 'It is used for Qty Ranges.'
            ]
        ]
    ];

    /**
     * @return ConfigValue
     */
    public function afterSave()
    {
        if ($this->isValueChanged() && $this->getValue() == '1') {
            $this->updateAttribute(
                $this->attributesForUpdate[$this->getField()]
            );
        }

        return parent::afterSave();
    }

    /**
     * @param array $attrInfo
     */
    private function updateAttribute($attrInfo)
    {
        /** @var Creator $attributeCreator */
        $attributeCreator = $this->getData('attribute_creator');

        if ($attributeCreator) {
            $attributeCreator->createProductAttribute(
                $attrInfo['code'],
                $attrInfo['label'],
                $attrInfo['args']
            );
        }
    }
}
