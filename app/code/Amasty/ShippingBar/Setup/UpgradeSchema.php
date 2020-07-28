<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */

namespace Amasty\ShippingBar\Setup;

use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Model\ResourceModel\Profile;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\AddColumns110
     */
    private $addColumns110;

    public function __construct(\Amasty\ShippingBar\Setup\Operation\AddColumns110\Proxy $addColumns110)
    {
        $this->addColumns110 = $addColumns110;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            $tableName = $setup->getTable(Profile::TABLE_NAME);

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->modifyColumn(
                    $tableName,
                    ProfileInterface::GOAL,
                    [
                        'type'      => Table::TYPE_FLOAT,
                        'length'    => '10,2'
                    ]
                );
            }
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addColumns110->execute($setup);
        }
        $setup->endSetup();
    }
}
