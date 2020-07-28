<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Setup\Operation;

use Amasty\RequestQuote\Api\Data\QuoteInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class ColumnUpdate
 */
class ColumnUpdate
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $table = $setup->getTable('amasty_quote');
        if (!$setup->getConnection()->tableColumnExists($table, QuoteInterface::ADMIN_NOTIFICATION_SEND)) {
            $setup->getConnection()->addColumn(
                $table,
                QuoteInterface::ADMIN_NOTIFICATION_SEND,
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'comment' => 'Admin Notification Sent'
                ]
            );
        }
        $setup->getConnection()->changeColumn(
            $table,
            'increment_id',
            'increment_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 32,
                'nullable' => true,
                'comment' => 'Quote Incement Id'
            ]
        );

        $setup->endSetup();
    }
}
