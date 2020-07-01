<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_PageBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	/**
     * @var \Magento\Eav\Model\Entity\Type
     */
	protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    
    /**
     * @var \Magento\Eav\Setup\EavSetupe
     */
    protected $_eavSetup;

    /**
     * @param \Magento\Eav\Setup\EavSetup         $eavSetup         
     * @param \Magento\Eav\Model\Entity\Type      $entityType       
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute 
     */
    public function __construct(
    	\Magento\Eav\Setup\EavSetup $eavSetup,
    	\Magento\Eav\Model\Entity\Type $entityType,
    	\Magento\Eav\Model\Entity\Attribute $catalogAttribute
    	) {
    	$this->_eavSetup = $eavSetup;
    	$this->_entityTypeModel = $entityType;
    	$this->_catalogAttribute = $catalogAttribute;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$entityTypeModel = $this->_entityTypeModel;
    	$catalogAttributeModel = $this->_catalogAttribute;
    	$installer =  $this->_eavSetup;

    	$setup->startSetup();

		/**
		 * Drop table if exists
		 */
		$setup->getConnection()->dropTable($setup->getTable('ves_blockbuilder_block'));
		$setup->getConnection()->dropTable($setup->getTable('ves_blockbuilder_cms'));
        $setup->getConnection()->dropTable($setup->getTable('ves_blockbuilder_product'));
        $setup->getConnection()->dropTable($setup->getTable('ves_blockbuilder_category'));
        $setup->getConnection()->dropTable($setup->getTable('ves_blockbuilder_page'));


 		/**
 		 * Create table 'ves_blockbuilder_block'
 		 */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('ves_blockbuilder_block'))
 		->addColumn(
 			'block_id',
 			Table::TYPE_INTEGER,
 			11,
 			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Block Id'
 			)
 		->addColumn(
 			'title',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Block title'
 			)
 		->addColumn(
 			'alias',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Block Alias Key'
 			)
        ->addColumn(
            'shortcode',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Block Shortcode'
            )
        ->addColumn(
            'show_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Profile Show From'
            )
        ->addColumn(
            'show_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Profile Show To'
            )
        ->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
        ->addColumn(
            'created',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'PageBuilder Creation Time'
            )
        ->addColumn(
            'modified',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'PageBuilder Modification Time'
            )
        ->addColumn(
            'customer_group',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Customer Group'
            )
        ->addColumn(
            'prefix_class',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Prefix Class'
            )
        ->addColumn(
            'block_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false, 'default' => 'block'],
            'Prefix Class'
            )
        ->addColumn(
            'page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Layout'
            )
        ->addColumn(
            'layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Page Layout Update Content'
            )
        ->addColumn(
            'params',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'params'
            )
        ->addColumn(
            'settings',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'settings'
            )
        ->addColumn(
            'container',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Container'
            )
        ->setComment('PageBuilder Profile');
        $setup->getConnection()->createTable($table);

 		

 		/**
         * Create table 'ves_blockbuilder_cms'
         */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('ves_blockbuilder_cms'))
 		->addColumn(
 			'block_id',
 			Table::TYPE_INTEGER,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Block Id'
 			)
        ->addColumn(
            'page_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Page Id'
            )
 		->addColumn(
 			'store_id',
 			Table::TYPE_SMALLINT,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Store Id'
 			)
 		->addIndex(
 			$setup->getIdxName('ves_blockbuilder_cms', ['page_id']),
 			['page_id']
 			)
        ->addIndex(
            $setup->getIdxName('ves_blockbuilder_cms', ['store_id']),
            ['store_id']
            )
 		->addForeignKey(
 			$setup->getFkName('ves_blockbuilder_cms', 'block_id', 'ves_blockbuilder_block', 'block_id'),
 			'block_id',
 			$setup->getTable('ves_blockbuilder_block'),
 			'block_id',
 			Table::ACTION_CASCADE
 			)
 		->addForeignKey(
 			$setup->getFkName('ves_blockbuilder_cms', 'store_id', 'store', 'store_id'),
 			'store_id',
 			$setup->getTable('store'),
 			'store_id',
 			Table::ACTION_CASCADE
 			)
 		->setComment('PageBuilder Block CMS Page');
 		$setup->getConnection()->createTable($table);

        /**
         * Create table 'ves_blockbuilder_product'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('ves_blockbuilder_product'))
        ->addColumn(
            'block_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Block Id'
            )
        ->addColumn(
            'product_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Page Id'
            )
        ->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
            )
        ->addIndex(
            $setup->getIdxName('ves_blockbuilder_product', ['product_id']),
            ['product_id']
            )
        ->addIndex(
            $setup->getIdxName('ves_blockbuilder_product', ['store_id']),
            ['store_id']
            )
        ->addForeignKey(
            $setup->getFkName('ves_blockbuilder_product', 'block_id', 'ves_blockbuilder_block', 'block_id'),
            'block_id',
            $setup->getTable('ves_blockbuilder_block'),
            'block_id',
            Table::ACTION_CASCADE
            )
        ->addForeignKey(
            $setup->getFkName('ves_blockbuilder_product', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
            )
        ->setComment('PageBuilder Block Product ');
        $setup->getConnection()->createTable($table);
        
        /**
         * Create table 'ves_blockbuilder_page'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('ves_blockbuilder_page'))
        ->addColumn(
            'block_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Block Id'
            )
        ->addColumn(
            'page_url',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Page Url key'
            )
        ->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
            )
        ->addIndex(
            $setup->getIdxName('ves_blockbuilder_page', ['page_url']),
            ['page_url']
            )
        ->addIndex(
            $setup->getIdxName('ves_blockbuilder_page', ['store_id']),
            ['store_id']
            )
        ->addForeignKey(
            $setup->getFkName('ves_blockbuilder_page', 'block_id', 'ves_blockbuilder_block', 'block_id'),
            'block_id',
            $setup->getTable('ves_blockbuilder_block'),
            'block_id',
            Table::ACTION_CASCADE
            )
        ->addForeignKey(
            $setup->getFkName('ves_blockbuilder_page', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
            )
        ->setComment('PageBuilder Block Other Pages ');

        $setup->getConnection()->createTable($table);


 		$setup->endSetup();
 	}
 }