<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Model\ResourceModel;

class Ranges extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_stockstatus_quantityranges', 'entity_id');
    }

    /**
     * Load an object by qty and rule
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $qty
     * @param array $rules
     * @return $this
     */
    public function loadByQtyAndRule(\Magento\Framework\Model\AbstractModel $object, $qty, array $rules = [])
    {
        $connection = $this->getConnection();
        if ($connection && $qty !== null) {
            $select = $this->getConnection()->select()->from(
                $this->getMainTable(),
                '*'
            )
                ->where($this->getMainTable() . '.qty_from <= ?', $qty)
                ->where($this->getMainTable() . '.qty_to >= ?', $qty);

            $ruleExpression = $this->_resources->getTableName('amasty_stockstatus_quantityranges') . '.rule = :param';
            $rulesCondition = '';
            $bind = [];
            foreach ($rules as $key => $rule) {
                if ($rule != null) {
                    $rulesCondition .= $ruleExpression . $key;
                    $bind['param' . $key] = $rule;
                }
            }

            if (count($rules) > 1) {
                $rulesCondition .= ' OR ' . $ruleExpression . count($rules);
                // range without rule
                $bind['param' . count($rules)] = 0;
            }

            if ($rulesCondition) {
                $select->where($rulesCondition);
                $select->order('rule desc');
            }

            $data = $connection->fetchRow($select, $bind);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

    public function deleteAll()
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable());
    }
}
