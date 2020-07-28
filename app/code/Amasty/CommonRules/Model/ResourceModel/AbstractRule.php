<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\ResourceModel;

/**
 * Rule skeleton.
 */
abstract class AbstractRule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ATTRIBUTE_TABLE_NAME = '';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Amasty\Base\Model\Serializer $serializer,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->serializer = $serializer;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($object->getConditionsSerialized()),
            $this->_getUsedAttributes($object->getActionsSerialized())
        );

        if (count($ruleProductAttributes)) {
            $this->saveAttributes($object->getId(), $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     *
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $serializedString = $this->serializer->unserialize($serializedString);

        if (is_array($serializedString) && array_key_exists('conditions', $serializedString)) {
            $result = $this->recursiveFindAttributes($serializedString);
        }

        return $result;
    }

    /**
     * @param $loop
     * @return array
     */
    protected function recursiveFindAttributes($loop)
    {
        $arrayIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($loop)
        );

        $result = [];
        $nextAttribute = false;
        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == \Amasty\CommonRules\Model\Rule::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $nextAttribute = true;
            }

            if ($key == 'attribute' && $nextAttribute) {
                $result[] = $value;
                $nextAttribute = false;
            }
        }

        return $result;
    }

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $dbConnection = $this->getConnection();
        $select = $dbConnection->select()
            ->from(
                $this->getTable(static::ATTRIBUTE_TABLE_NAME),
                new \Zend_Db_Expr('DISTINCT code')
            );

        return $dbConnection->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $ruleId
     * @param array $attributes
     *
     * @return $this
     */
    public function saveAttributes($ruleId, $attributes)
    {
        $dbConnection = $this->getConnection();

        $dbConnection->delete(
            $this->getTable(static::ATTRIBUTE_TABLE_NAME),
            [
                'rule_id = ?' => $ruleId
            ]
        );

        $data = [];
        foreach ($attributes as $code) {
            $data[] = [
                'rule_id' => $ruleId,
                'code' => $code,
            ];
        }
        $dbConnection->insertMultiple($this->getTable(static::ATTRIBUTE_TABLE_NAME), $data);

        return $this;
    }

    /**
     * Returns linked stores
     *
     * @param \Amasty\CommonRules\Model\Rule $model
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStores($model)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable(),
            'stores'
        )->where(
            'rule_id = :rule_id'
        );

        if (!($result = $connection->fetchCol($select, ['rule_id' => $model->getId()]))) {
            $result = [];
        }

        if (is_string($result)) {
            $result = explode(',', $result);
        }

        return $result;
    }
}
