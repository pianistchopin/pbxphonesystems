<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Setup;

use Amasty\ShippingArea\Model\System\ConditionOptionProvider;
use Amasty\ShippingArea\Api\Data\AreaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = \Amasty\ShippingArea\Model\ResourceModel\Area::MAIN_TABLE;
        if (!$installer->tableExists($tableName)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
            ->addColumn(
                AreaInterface::AREA_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'ID'
            )
            ->addColumn(
                AreaInterface::NAME,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                AreaInterface::DESCRIPTION,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true]
            )
            ->addColumn(
                AreaInterface::IS_ENABLED,
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                1,
                ['nullable' => false, 'default' => 0]
            )
            ->setComment('Shipping Areas by Amasty (Set of address conditions)');

            foreach (['country', 'state', 'city', 'postcode', 'address'] as $fieldName) {
                $table->addColumn(
                    $fieldName . '_condition',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => ConditionOptionProvider::CONDITION_ALL]
                )->addColumn(
                    $fieldName . '_set',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true]
                );
            }

            $table->addColumn(
                AreaInterface::STATE_SET_LISTING,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true]
            );

            //TODO: Add Indexes

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
