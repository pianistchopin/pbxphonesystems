<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Operation\HideQtyBased
     */
    private $hideQtyBased;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Operation\HideQtyBased $hideQtyBased
    ) {

        $this->eavSetupFactory = $eavSetupFactory;
        $this->hideQtyBased = $hideQtyBased;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updateQtyBasedAttr($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->updateNotesForAttributes($setup);
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->hideQtyBasedAttribute($setup);
        }
    }

    /**
     * @param $setup
     */
    private function updateQtyBasedAttr($setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributeIdQtyBased = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'custom_stock_status_qty_based'
        );
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeIdQtyBased,
            'frontend_input',
            'boolean'
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function updateNotesForAttributes($setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributeIdStockStatus = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'custom_stock_status'
        );
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeIdStockStatus,
            'note',
            'It will be shown if Qty Ranges are not applied.'
        );

        $attributeIdQtyRule = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'custom_stock_status_qty_rule'
        );
        if ($attributeIdQtyRule) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeIdQtyRule,
                'note',
                'It is used for Qty Ranges.'
            );
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeIdQtyRule,
                'frontend_label',
                'Custom Stock Status Range Product Group'
            );
        }
    }

    /**
     * Attribute must showing manually
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function hideQtyBasedAttribute(ModuleDataSetupInterface $setup)
    {
        $this->hideQtyBased->execute($this->eavSetupFactory, $setup);
    }
}
