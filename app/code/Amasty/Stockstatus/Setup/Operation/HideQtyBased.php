<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Setup\Operation;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class HideQtyBased
{
    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(EavSetupFactory $eavSetupFactory, ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $eavSetupFactory->create(['setup' => $setup]);

        $qtyBasedAttrId = $eavSetup->getAttributeId(
            Product::ENTITY,
            'custom_stock_status_qty_based'
        );

        $setup->updateTableRow(
            'catalog_eav_attribute',
            'attribute_id',
            $qtyBasedAttrId,
            'is_visible',
            0
        );
    }
}
