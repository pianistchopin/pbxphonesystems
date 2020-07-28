<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Setup;

use Amasty\ShippingBar\Api\Data\LabelInterface;
use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Model\ResourceModel\Label;
use Amasty\ShippingBar\Model\ResourceModel\Profile;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists(Profile::TABLE_NAME)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Profile::TABLE_NAME))
                ->addColumn(
                    ProfileInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )->addColumn(
                    ProfileInterface::NAME,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Profile name'
                )->addColumn(
                    ProfileInterface::STATUS,
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'default' => 0],
                    'Active/inactive'
                )->addColumn(
                    ProfileInterface::GOAL,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false],
                    'Order amount'
                )->addColumn(
                    ProfileInterface::GOAL_SOURCE,
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => \Amasty\ShippingBar\UI\OptionsProviders\GoalSource::USE_GOAL],
                    'Goal Source'
                )->addColumn(
                    ProfileInterface::PRIORITY,
                    Table::TYPE_SMALLINT,
                    null,
                    ['default' => 1, 'nullable' => false],
                    'Priority'
                )->addColumn(
                    ProfileInterface::STORES,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Store Views'
                )->addColumn(
                    ProfileInterface::CUSTOMER_GROUPS,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Customer Groups'
                )->addColumn(
                    ProfileInterface::POSITION,
                    Table::TYPE_INTEGER,
                    null,
                    [],
                    'Bar position'
                )->addColumn(
                    ProfileInterface::PAGES,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Show on pages'
                )->addColumn(
                    ProfileInterface::ACTION_CLICKABLE,
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'default' => 0],
                    'Action by click'
                )->addColumn(
                    ProfileInterface::ACTION_LINK,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Action link'
                )->addColumn(
                    ProfileInterface::CLOSEABLE,
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'default' => 0],
                    'Can be closed'
                )->addColumn(
                    ProfileInterface::CAR_ICON_VISIBLE,
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'default' => 1],
                    'Car visibility'
                )->addColumn(
                    ProfileInterface::TEXT_FONT,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Text font'
                )->addColumn(
                    ProfileInterface::TEXT_SIZE,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Text size'
                )->addColumn(
                    ProfileInterface::TEXT_COLOR,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Text color'
                )->addColumn(
                    ProfileInterface::EXTRA_COLOR,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Color of goal and action budges'
                )->addColumn(
                    ProfileInterface::BACKGROUND_COLOR,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Bar background color'
                )->addColumn(
                    ProfileInterface::CUSTOM_STYLE,
                    Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Custom css'
                )->addIndex(
                    $installer->getIdxName(Profile::TABLE_NAME, [ProfileInterface::PRIORITY]),
                    [ProfileInterface::PRIORITY]
                )->setComment('Amasty Shipping Bar Profile');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(Label::TABLE_NAME)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Label::TABLE_NAME)
            )->addColumn(
                LabelInterface::LABEL_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Label Id'
            )->addColumn(
                LabelInterface::PROFILE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Profile Id'
            )->addColumn(
                LabelInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                LabelInterface::ACTION,
                Table::TYPE_TEXT,
                64,
                ['unsigned' => true, 'nullable' => false],
                'Profile Id'
            )->addColumn(
                LabelInterface::LABEL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Label'
            )->addIndex(
                $installer->getIdxName(Label::TABLE_NAME, [LabelInterface::STORE_ID]),
                [LabelInterface::STORE_ID]
            )->addForeignKey(
                $installer->getFkName(
                    Label::TABLE_NAME,
                    LabelInterface::PROFILE_ID,
                    Profile::TABLE_NAME,
                    ProfileInterface::ID
                ),
                LabelInterface::PROFILE_ID,
                $installer->getTable(Profile::TABLE_NAME),
                ProfileInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(Label::TABLE_NAME, LabelInterface::STORE_ID, 'store', LabelInterface::STORE_ID),
                LabelInterface::STORE_ID,
                $installer->getTable('store'),
                LabelInterface::STORE_ID,
                Table::ACTION_CASCADE
            )->setComment('Amasty Shipping Bar Profile Label');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
