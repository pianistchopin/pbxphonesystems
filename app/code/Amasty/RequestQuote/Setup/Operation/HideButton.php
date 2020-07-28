<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;

class HideButton
{
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    private $eavSetup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavSetup = $eavSetup;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute($setup)
    {
        $this->eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            \Amasty\RequestQuote\Helper\Data::ATTRIBUTE_NAME_HIDE_BUY_BUTTON,
            [
                'group' => 'General',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Hide \'Add to Quote\' button',
                'input' => 'boolean',
                'class' => '',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,grouped,downloadable'
            ]
        );

        $this->eavConfig->clear();
    }
}
