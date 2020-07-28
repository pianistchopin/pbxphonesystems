<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * phpcs:ignoreFile
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->updateExistCouponField($setup, 'discount_id', 'Discount ID');
            $this->updateExistCouponField($setup, 'discount_id_disable', 'Disable Discount ID');
        }

        $setup->endSetup();
    }

    protected function updateExistCouponField(SchemaSetupInterface $setup, $field, $comment)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('amasty_shiprestriction_rule'),
            $field,
            $field,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'comment' => $comment
            ]
        );
    }
}
