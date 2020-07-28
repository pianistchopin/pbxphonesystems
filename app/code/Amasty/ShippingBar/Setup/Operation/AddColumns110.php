<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Setup\Operation;

use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Model\ResourceModel\Profile;
use Magento\Framework\DB\Ddl\Table;

class AddColumns110
{
    public function execute(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(Profile::TABLE_NAME);
        if ($setup->getConnection()->isTableExists($tableName)) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $tableName,
                ProfileInterface::STORES,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Store Views',
                    'after' => 'websites'
                ]
            );

            $this->convertWebsitesToStores($connection, $tableName);

            $connection->dropColumn($tableName, 'websites');

            $connection->addColumn(
                $tableName,
                ProfileInterface::GOAL_SOURCE,
                [
                    'type' => Table::TYPE_SMALLINT,
                    'default' => \Amasty\ShippingBar\UI\OptionsProviders\GoalSource::USE_GOAL,
                    'nullable' => false,
                    'comment' => 'Goal Source',
                    'after' => ProfileInterface::GOAL
                ]
            );
            $connection->addColumn(
                $tableName,
                ProfileInterface::PRIORITY,
                [
                    'type' => Table::TYPE_SMALLINT,
                    'default' => 1,
                    'nullable' => false,
                    'comment' => 'Priority',
                    'after' => ProfileInterface::GOAL_SOURCE
                ]
            );
            $connection->addIndex(
                $tableName,
                $setup->getIdxName(Profile::TABLE_NAME, [ProfileInterface::PRIORITY]),
                [ProfileInterface::PRIORITY]
            );
        }
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $tableName
     */
    protected function convertWebsitesToStores($connection, $tableName)
    {
        $select = $connection->select();
        $select->from($tableName, [ProfileInterface::ID, 'websites']);
        $rowSet = $connection->fetchAll($select);

        foreach ($rowSet as $row) {
            if (!empty($row['websites'])) {
                $websites = explode(',', $row['websites']);

                $storesSelect = $connection->select()
                    ->from('store', ['store_id'])
                    ->where('website_id IN (?)', $websites);

                $storesArray = $connection->fetchCol($storesSelect);

                $stores = null;
                if ($storesArray) {
                    $stores = implode(',', $storesArray);
                }

                $connection->update(
                    $tableName,
                    [ProfileInterface::STORES => $stores],
                    ['id = ?' => $row[ProfileInterface::ID]]
                );
            }
        }
    }
}
