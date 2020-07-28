<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class SubmitedDate
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_quote'),
            'submited_date',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'comment' => 'Submited Date'
            ]
        );

        $setup->endSetup();
    }
}
