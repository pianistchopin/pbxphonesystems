<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Setup;

/**
 * phpcs:ignoreFile
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    /**
     * @var \Amasty\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Amasty\Shiprestriction\Setup\Operation\ChangeMethodsFormat
     */
    private $changeMethodsFormat;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Amasty\Base\Setup\SerializedFieldDataConverter $fieldDataConverter,
        \Magento\Framework\App\State $appState,
        \Amasty\Shiprestriction\Setup\Operation\ChangeMethodsFormat\Proxy $changeMethodsFormat
    ) {
        $this->productMetaData = $productMetaData;
        $this->fieldDataConverter = $fieldDataConverter;
        $this->appState = $appState;
        $this->changeMethodsFormat = $changeMethodsFormat;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $this->fieldDataConverter->convertSerializedDataToJson(
                'amasty_shiprestriction_rule',
                'rule_id',
                ['conditions_serialized']
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.2.3', '<')) {
            $this->appState->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_ADMINHTML,
                [$this->changeMethodsFormat, 'execute']
            );
        }

        $setup->endSetup();
    }
}
