<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Setup;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->dropTable($installer->getTable(\Amasty\ShippingArea\Model\ResourceModel\Area::MAIN_TABLE));

        $installer->endSetup();
    }
}
