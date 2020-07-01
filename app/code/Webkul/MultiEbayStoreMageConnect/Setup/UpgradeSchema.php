<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Update tables 'wk_multiebaysynchronize_category'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebaysynchronize_category'),
            'ean_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'EAN Status',
                'length' => '255',
                'after' => 'variations_enabled'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebaysynchronize_category'),
            'upc_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'UPC Status',
                'length' => '255',
                'after' => 'ean_status'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebaysynchronize_category'),
            'attribute_set',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Store attribute set id',
                'after' => 'entity_id'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebaysynchronize_product'),
            'sku',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'Product SKU on magento',
                'length' => '255',
                'after' => 'magento_pro_id'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'template_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'listing template id',
            ]
        );

        /*
         * Create table 'wk_multiebay_listing_template'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('wk_multiebay_listing_template'))
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
                'template_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Template Title'
            )->addColumn(
                'template_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Product Content'
            )->addColumn(
                'mapped_attribute',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Mapped Attribute With Template Content'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Template Create Time'
            )->addIndex(
                $setup->getIdxName('wk_multiebay_listing_template', ['entity_id']),
                ['entity_id']
            )->setComment('eBay Listing Template');
        $setup->getConnection()->createTable($table);

        // add more columns in wk_multiebay_seller_details table
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'revise_item',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'revise eBay item',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'default_cate',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'default category',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'default_store_view',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'default store view',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'item_speci',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'import item specification or not',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'item_with_html',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'item with html or not',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'import_product',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'import product from ebay ex. mapped categories/all products',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'default_qty',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'default qty for export product',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'template_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'template id for export product',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_free',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'ship free enable/disable',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'price_rule_on',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'price rule applied on',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'shipping_service',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'shipping service for export',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_cost',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'shiping cost for export',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_aditional_cost',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'shiping additional cost for export',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_min_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'ship min time',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_max_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'ship max time',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'product_type_allowed',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'export only selected product types',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'paypal_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default_value' => '0',
                'comment' => 'paypal id for export',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'order_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'ebay order status',
                'length' => '255',
            ]
        );
        // return policy
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'dispatch_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'dispatch time of ebay product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'listing_duration',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'listing duration of ebay product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'return_policy',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'return policy for export product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'pay_by',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'pay by for export product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'returns_within',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'return within for ebay product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'other_info',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'other information for export product',
                'length' => '255',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_multiebay_seller_details'),
            'ship_priority',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'shipping service periority',
                'length' => '255',
            ]
        );

        /**
         * Create table 'wk_multiebay_product_pricerule
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('wk_multiebay_product_pricerule'))
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
            'price_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Price From'
        )->addColumn(
            'price_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Price To'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'product sku'
        )->addColumn(
            'operation',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Product Price Operation'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Price'
        )->addColumn(
            'ebay_account_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ebay account id'
        )->addColumn(
            'operation_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Operation Type ex. fixed/percent'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [],
            'status of rule'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
            ],
            'rule created time'
        )->addIndex(
            $setup->getIdxName('wk_multiebay_product_pricerule', ['entity_id']),
            ['entity_id']
        )->setComment('eBay Product Price Rule');

        $setup->getConnection()->createTable($table);


        $setup->endSetup();
    }
}
