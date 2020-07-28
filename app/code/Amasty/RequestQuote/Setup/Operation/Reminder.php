<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class Reminder
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_quote'),
            'expired_date',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => true,
                'comment' => 'Expiration Date'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_quote'),
            'reminder_date',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => true,
                'comment' => 'Reminder Date'
            ]
        );

        $setup->endSetup();
    }
}
