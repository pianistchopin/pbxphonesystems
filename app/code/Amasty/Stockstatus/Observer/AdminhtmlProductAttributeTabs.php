<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Observer;

use Magento\Framework\Event\ObserverInterface;

class AdminhtmlProductAttributeTabs implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->_coreRegistry = $registry;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs)
        {
            $attributeObject = $this->_coreRegistry->registry('entity_attribute');
            if ("custom_stock_status" === $attributeObject->getAttributeCode()) {
                $block->addTabAfter(
                    'icons',
                    [
                        'label' => __('Manage Icons'),
                        'title' => __('Manage Icons'),
                        'content' => $block->getChildHtml('icons'),

                    ],
                    'front'
                );
                $block->addTabAfter(
                    'ranges',
                    [
                        'label' => __('Quantity Range Statuses'),
                        'title' => __('Quantity Range Statuses'),
                        'content' => $block->getChildHtml('ranges'),
                    ],
                    'front'
                );
            }
        }
    }
}
