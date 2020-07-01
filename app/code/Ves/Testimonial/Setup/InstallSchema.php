<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('ves_testimonial_testimonial'));
        $setup->getConnection()->dropTable($setup->getTable('ves_testimonial_testimonial_store'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial')
        )
        ->addColumn(
            'testimonial_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Testimonial ID'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'NickName'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )
        ->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )
        ->addColumn(
            'company_address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company Address'
        )
        ->addColumn(
            'company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company Name'
        )->addColumn(
            'company_website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Company WebSite'
        )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Position'
        )
        ->addColumn(
            'linkedin',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Linkedin'
        )
        ->addColumn(
            'facebook',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Facebook'
        )
        ->addColumn(
            'twitter',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Twitter'
        )
        ->addColumn(
            'youtube',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Youtube'
        )
        ->addColumn(
            'vimeo',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Vimeo'
        )
        ->addColumn(
            'googleplus',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Google Plus'
        )
        ->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Address'
        )
        ->addColumn(
            'testimonial',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Testimonial'
        )
        ->addColumn(
            'create_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Create Time'
        )
        ->addColumn(
            'rating',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Rating'
        )
        ->addColumn(
            'job',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job'
        )  
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Number Column on Tablets'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )        
        ->addIndex(
            $setup->getIdxName(
                $installer->getTable('ves_testimonial_testimonial'),
                ['name', 'testimonial', 'email'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name', 'testimonial', 'email'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )
        ->setComment(
            'Testimonial - Testimonial Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'ves_blog_category_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_testimonial_testimonial_store')
        )->addColumn(
            'testimonial_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Testimonial ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('ves_testimonial_testimonial_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('ves_testimonial_testimonial_store', 'testimonial_id', 'ves_testimonial_testimonial', 'category_id'),
            'testimonial_id',
            $installer->getTable('ves_testimonial_testimonial'),
            'testimonial_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_testimonial_testimonial_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'CMS Page To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);
    }
}