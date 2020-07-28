<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Setup\Operation;

use Amasty\ElasticSearch\Api\Data\RelevanceRuleInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Store\Model\Website;

/**
 * Class CreateStemmedWordsScheme
 * @package Amasty\ElasticSearch\Setup\Operation
 */
class CreateStemmedWordsScheme
{
    /**
     * @param SchemaSetupInterface $installer
     * @throws \Exception
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('amasty_elastic_stemmed_word')
        )->addColumn(
            'stemmed_word_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unique' => true,  'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Stemmed Word Id'
        )->addColumn(
            'stemmed_word',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,  'unique' => true],
            'Stemmed Word'
        )->addColumn(
            'words',
            Table::TYPE_TEXT,
            1024,
            ['nullable' => false,],
            'Words'
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Store Id'
        )->addIndex(
            $installer->getIdxName('amasty_elastic_stop_word', ['stemmed_word']),
            ['stemmed_word']
        )->addIndex(
            $installer->getIdxName(
                'amasty_elastic_stemmed_word',
                ['stemmed_word', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['stemmed_word', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Stemmed Words Data'
        );

        $installer->getConnection()->createTable($table);
    }
}
