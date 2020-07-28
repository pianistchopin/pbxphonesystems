<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */

namespace Amasty\Stockstatus\Setup;

use Magento\Eav\Model\Entity\Attribute\Source\Boolean as SourceBoolean;
use Magento\Eav\Model\Entity\Attribute\Source\Table as SourceTable;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\Stockstatus\Model\Attribute\Creator;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Creator
     */
    private $attributeCreator;

    /**
     * @var Operation\Examples
     */
    private $examples;

    public function __construct(Creator $attributeCreator, Operation\Examples $examples)
    {
        $this->attributeCreator = $attributeCreator;
        $this->examples = $examples;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->attributeCreator->createProductAttribute(
            'custom_stock_status',
            'Custom Stock Status',
            [
                'source' => SourceTable::class
            ]
        );

        $this->attributeCreator->createProductAttribute(
            'custom_stock_status_qty_based',
            'Use Quantity Ranges Based Stock Status',
            [
                'input' => 'boolean',
                'source' => SourceBoolean::class,
                'user_defined' => false
            ]
        );

        $tableName = $setup->getTable('amasty_stockstatus_quantityranges');
        $setup->run("
            CREATE TABLE IF NOT EXISTS `{$tableName}`  (
                `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `qty_from` INT NOT NULL ,
                `qty_to` INT NOT NULL ,
                `rule` INT NULL,
                `status_id` INT UNSIGNED NOT NULL
            ) ENGINE = InnoDB ;
        ");

        $this->examples->execute();
    }
}

