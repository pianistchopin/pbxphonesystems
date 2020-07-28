<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\Reminder
     */
    private $reminder;

    /**
     * @var Operation\SubmitedDate
     */
    private $submitedDate;

    /**
     * @var Operation\ColumnUpdate
     */
    private $columnUpdate;

    public function __construct(
        Operation\Reminder $reminder,
        Operation\SubmitedDate $submitedDate,
        Operation\ColumnUpdate $columnUpdate
    ) {
        $this->reminder = $reminder;
        $this->submitedDate = $submitedDate;
        $this->columnUpdate = $columnUpdate;
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->reminder->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.4.4', '<')) {
            $this->submitedDate->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->columnUpdate->execute($setup);
        }
    }
}
