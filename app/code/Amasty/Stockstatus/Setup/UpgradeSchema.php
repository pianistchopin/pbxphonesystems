<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Amasty\StockStatus\Setup\Operation\RenameConfig
     */
    private $renameConfig;

    /**
     * @var Operation\MoveFields
     */
    private $moveFields;

    public function __construct(
        \Amasty\Stockstatus\Setup\Operation\RenameConfig $renameConfig,
        \Amasty\Stockstatus\Setup\Operation\MoveFields $moveFields
    ) {
        $this->renameConfig = $renameConfig;
        $this->moveFields = $moveFields;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.10', '<')) {
            $this->renameConfig->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->moveFields->execute($setup);
        }

        $setup->endSetup();
    }
}
