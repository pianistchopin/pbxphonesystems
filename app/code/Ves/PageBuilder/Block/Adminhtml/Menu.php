<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Block\Adminhtml;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Ves_PageBuilder::menu.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                'pagebuilder' => [
                    'title' => __('Manage Pages'),
                    'url' => $this->getUrl('*/pagebuilder/index'),
                    'resource' => 'Ves_PageBuilder::page',
                    'child' => [
                        'newAction' => [
                            'title' => __('New Page'),
                            'url' => $this->getUrl('*/pagebuilder/newAction'),
                            'resource' => 'Ves_PageBuilder::page_edit',
                        ]
                    ]
                ],
                'blockbuilder' => [
                    'title' => __('Manage Elements'),
                    'url' => $this->getUrl('*/blockbuilder/index'),
                    'resource' => 'Ves_PageBuilder::block',
                    'child' => [
                        'newAction' => [
                            'title' => __('New Element Profile'),
                            'url' => $this->getUrl('*/blockbuilder/newAction'),
                            'resource' => 'Ves_PageBuilder::block_edit',
                        ]
                    ]
                ],
                'basewidget' => [
                    'title' => __('Manage Images'),
                    'url' => $this->getUrl('vesbasewidget/basewidget/imagemanager'),
                    'resource' => 'Ves_BaseWidget::baseconnector'
                ],
                'base_system_config' => [
                    'title' => __('Base Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'vesbasewidget']),
                    'resource' => 'Ves_BaseWidget::config_basewidget',
                    'separator' => true
                ],
                'page_system_config' => [
                    'title' => __('Page Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'vespagebuilder']),
                    'resource' => 'Ves_PageBuilder::config_pagebuilder'
                ],
                'livecss_system_config' => [
                    'title' => __('Live Css Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'veslivecss']),
                    'resource' => 'Ves_PageBuilder::config_livecss'
                ],
                'readme' => [
                    'title' => __('Guide'),
                    'url' => 'http://www.venustheme.com/guide-ves-pages-builder-magento-2/',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'https://venustheme.ticksy.com',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]
            ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }
        return $this->items;
    }

    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }
        return $items['page'];
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }
        return $result;
    }

    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }
}
