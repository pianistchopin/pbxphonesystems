<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */

namespace Amasty\Pgrid\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateQtySoldTable
     */
    private $qtySoldTable;

    /**
     * InstallSchema constructor.
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
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->qtySoldTable->execute($setup);
        $installer->endSetup();
    }
}
