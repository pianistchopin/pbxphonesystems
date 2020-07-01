<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->moveDirToLibDir();
        $installer = $setup;
        $installer->startSetup();

        /*
         * Create table 'wk_multiebaysynchronize_product'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebaysynchronize_product'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'magento_pro_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Product Id'
            )->addColumn(
                'ebay_pro_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay Product Id'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Product Name'
            )->addColumn(
                'product_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Product Type'
            )->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Product Price'
            )->addColumn(
                'magento_pro_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Product Id'
            )->addColumn(
                'mage_cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Category Id'
            )->addColumn(
                'change_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Change Status'
            )->addColumn(
                'created',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Product sync Time'
            )->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'eBay account id'
            )->addIndex(
                $installer->getIdxName('wk_multiebaysynchronize_product', ['entity_id']),
                ['entity_id']
            )->setComment('eBay Synchronize Product');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_multiebaysynchronize_category'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebaysynchronize_category'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'mage_cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Category Id'
            )->addColumn(
                'ebay_cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ebay Category Id'
            )->addColumn(
                'ebay_cat_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Ebay Category Name'
            )->addColumn(
                'pro_condition_attr',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Product Condition Attribute'
            )->addColumn(
                'variations_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Variations Enabled'
            )->addColumn(
                'created',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Category Mapped Time'
            )->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'customer rule id'
            )->addIndex(
                $installer->getIdxName('wk_multiebaysynchronize_category', ['entity_id']),
                ['entity_id']
            )->setComment('Ebay Synchronize Category Table');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_multiebay_categories'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebay_categories'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'ebay_cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ebay Category Id'
            )->addColumn(
                'ebay_cat_parentid',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ebay parent Category Id'
            )->addColumn(
                'ebay_cat_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Ebay Category Name'
            )->addColumn(
                'created',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Category Mapped Time'
            )->addIndex(
                $installer->getIdxName('wk_multiebay_categories', ['entity_id']),
                ['entity_id']
            )->setComment('Ebay Categories Table');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_multiebaysynchronize_order'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebaysynchronize_order'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'ebay_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Ebay Order Id'
            )->addColumn(
                'mage_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Magento Order Id'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Order Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Order Sync Time'
            )->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'customer rule id'
            )->addIndex(
                $installer->getIdxName('wk_multiebaysynchronize_order', ['entity_id']),
                ['entity_id']
            )->setComment('Ebay Synchronize Order Table');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_ebaysynchronize_specification_category'
         */

        $table = $installer->getConnection()
            ->newTable(
                $installer
                    ->getTable('wk_multiebaysynchronize_category_specification')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'ebay_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Ebay Category Id'
            )->addColumn(
                'ebay_specification_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'eBay Specification Name'
            )->addColumn(
                'mage_product_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Magento Product Attribute Code'
            )->addColumn(
                'created',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Specification Sync Time'
            )->addIndex(
                $installer->getIdxName('wk_multiebaysynchronize_category_specification', ['entity_id']),
                ['entity_id']
            )->setComment('eBay Synchronize Category Specifications Table');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_multimpebay_tempebay'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebay_tempebay'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'item_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Idenityfy that order or product'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'eBay Item Id'
            )->addColumn(
                'product_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'eBay item data in json format'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Import Time'
            )->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'customer rule id'
            )->addIndex(
                $installer->getIdxName('wk_multiebay_tempebay', ['entity_id']),
                ['entity_id']
            )->setComment('eBay imported products temp table');

        $installer->getConnection()->createTable($table);

                /*
         * Create table 'wk_multiebay_seller_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_multiebay_seller_details'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity Id'
            )->addColumn(
                'attribute_set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'attribute set id'
            )->addColumn(
                'store_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay store name'
            )->addColumn(
                'global_site',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay Product Id'
            )->addColumn(
                'ebay_user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'ebay user id'
            )->addColumn(
                'ebay_authentication_token',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2056,
                [],
                'Ebay authontication token'
            )->addColumn(
                'ebay_developer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay developer id'
            )->addColumn(
                'ebay_application_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay application id'
            )->addColumn(
                'ebay_certification_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay Certification id'
            )->addColumn(
                'shop_postal_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Ebay shop postal code'
            )->addIndex(
                $installer->getIdxName('wk_multiebay_seller_details', ['entity_id']),
                ['entity_id']
            )->setComment('eBay Sellers Details');

        $installer->getConnection()->createTable($table);
        /****/

        $installer->endSetup();
        $this->addForeignKeys($setup);
    }

    public function addForeignKeys($setup)
    {
        /**
         * Add foreign keys for table wk_multiebaysynchronize_product
         */
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'wk_multiebaysynchronize_product',
                'magento_pro_id',
                'catalog_product_entity',
                'entity_id'
            ),
            $setup->getTable('wk_multiebaysynchronize_product'),
            'magento_pro_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        /**
         * Add foreign keys for table wk_multiebaysynchronize_category
         */
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'wk_multiebaysynchronize_category',
                'mage_cat_id',
                'catalog_category_entity',
                'entity_id'
            ),
            $setup->getTable('wk_multiebaysynchronize_category'),
            'mage_cat_id',
            $setup->getTable('catalog_category_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        /**
         * Add foreign keys for table wk_multiebaysynchronize_order
         */
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'wk_multiebaysynchronize_order',
                'rule_id',
                'wk_multiebay_seller_details',
                'entity_id'
            ),
            $setup->getTable('wk_multiebaysynchronize_order'),
            'rule_id',
            $setup->getTable('wk_multiebay_seller_details'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        /**
         * Add foreign keys for table wk_multiebaysynchronize_order
         */
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'wk_multiebaysynchronize_product',
                'rule_id',
                'wk_multiebay_seller_details',
                'entity_id'
            ),
            $setup->getTable('wk_multiebaysynchronize_product'),
            'rule_id',
            $setup->getTable('wk_multiebay_seller_details'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        /**
         * Add foreign keys for table wk_multiebaysynchronize_category
         */
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'wk_multiebaysynchronize_category',
                'rule_id',
                'wk_multiebay_seller_details',
                'entity_id'
            ),
            $setup->getTable('wk_multiebaysynchronize_category'),
            'rule_id',
            $setup->getTable('wk_multiebay_seller_details'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
    }

    /**
     * move ebay directory to lib dirctory
     */
    private function moveDirToLibDir()
    {
        try {
            /** @var \Magento\Framework\ObjectManagerInterface $objManager */
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Module\Dir\Reader $reader */
            $reader = $objManager
                        ->get('Magento\Framework\Module\Dir\Reader');

            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $objManager->get('Magento\Framework\Filesystem');

            $libEbayFullPath = $filesystem
                                ->getDirectoryRead(DirectoryList::LIB_INTERNAL)
                                ->getAbsolutePath('Ebay');
            if (!file_exists($libEbayFullPath)) {
                mkdir($libEbayFullPath, 0777, true);
                $libFile = $reader->getModuleDir('', 'Webkul_MultiEbayStoreMageConnect').'/lib/Ebay/';
                $ebayLibFiles = scandir($libFile, 1);
                foreach ($ebayLibFiles as $ebaylibFile) {
                    if (is_file($libFile.$ebaylibFile)) {
                        copy($libFile.$ebaylibFile, $libEbayFullPath.'/'.$ebaylibFile);
                        unlink($libFile.$ebaylibFile);
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
