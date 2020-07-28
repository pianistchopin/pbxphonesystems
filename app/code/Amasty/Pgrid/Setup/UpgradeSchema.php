<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\CreateQtySoldTable
     */
    private $qtySoldTable;

    /**
     * UpgradeSchema constructor.
     *
     * @param Operation\CreateQtySoldTable $qtySoldTable
     */
    public function __construct(
        Operation\CreateQtySoldTable $qtySoldTable
    ) {
        $this->qtySoldTable = $qtySoldTable;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->qtySoldTable->execute($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.5.1', '<')) {
            $this->qtySoldTable->removeIndex($setup);
        }

        $setup->endSetup();
    }
}
