<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Model\Wysiwyg;

class CompositeConfigProvider extends \Magento\Cms\Model\Wysiwyg\CompositeConfigProvider
{
    /**
     * @param \Magento\Ui\Block\Wysiwyg\ActiveEditor $activeEditor
     * @param ConfigProviderFactory $configProviderFactory
     * @param array $variablePluginConfigProvider
     * @param array $widgetPluginConfigProvider
     * @param array $galleryConfigProvider
     * @param array $wysiwygConfigPostProcessor
     */
    public function __construct(
        \Magento\Ui\Block\Wysiwyg\ActiveEditor $activeEditor,
        \Magento\Cms\Model\Wysiwyg\ConfigProviderFactory $configProviderFactory,
        array $variablePluginConfigProvider = [],
        array $widgetPluginConfigProvider = [],
        array $galleryConfigProvider = [],
        array $wysiwygConfigPostProcessor = []
    ) {
        parent::__construct(
            $activeEditor,
            $configProviderFactory,
            $variablePluginConfigProvider,
            $widgetPluginConfigProvider,
            $galleryConfigProvider,
            $wysiwygConfigPostProcessor
        );
    }
}
