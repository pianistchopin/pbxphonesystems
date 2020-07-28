<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Setup;

use Amasty\ShippingBar\Model\ResourceModel\Profile;
use Amasty\ShippingBar\Model\ResourceModel\Label;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->dropTable($installer->getTable(Label::TABLE_NAME));
        $installer->getConnection()->dropTable($installer->getTable(Profile::TABLE_NAME));

        $installer->endSetup();
    }
}
