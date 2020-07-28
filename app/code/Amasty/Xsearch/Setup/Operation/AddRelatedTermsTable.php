<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Setup\Operation;

use \Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddRelatedTermsTable
 */
class AddRelatedTermsTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'catalog_category_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('amasty_xsearch_related_term'))
            ->addColumn(
                'term_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Term ID'
            )->addColumn(
                'related_term_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Related Term ID'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $setup->getIdxName('amasty_xsearch_related_term', ['term_id']),
                ['term_id']
            )->addIndex(
                $setup->getIdxName(
                    'amasty_xsearch_related_term',
                    ['term_id', 'related_term_id', 'position'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['term_id', 'related_term_id', 'position'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'amasty_xsearch_related_term',
                    'term_id',
                    'search_query',
                    'query_id'
                ),
                'term_id',
                $setup->getTable('search_query'),
                'query_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Related Terms Linking Table');
        $setup->getConnection()->createTable($table);
    }
}
