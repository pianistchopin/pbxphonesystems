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
 * @package    Ves_PageBuilder
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\PageBuilder\Block\Adminhtml\Pagebuilder\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Cms extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $_pageLayout;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Theme\Model\Layout\Source\Layout $pageLayout
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form tab configuration
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $this->_eventManager->dispatch(
        'ves_check_license',
        ['obj' => $this,'ex'=>'Ves_PageBuilder']
        );
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Ves_PageBuilder::page_edit');

        if (($this->hasData('is_valid') && $this->hasData('local_valid')) && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
        }
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['html_id_prefix' => 'block_']]);

        $model = $this->_coreRegistry->registry('ves_pagebuilder');

        $layoutFieldset = $form->addFieldset(
            'layout_fieldset',
            ['legend' => __('Page Layout'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        $layoutFieldset->addField(
            'page_layout',
            'select',
            [
                'name' => 'page_layout',
                'label' => __('Layout'),
                'required' => true,
                'values' => $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setRootTemplate($this->_pageLayout->getDefaultValue());
            $model->setPageLayout($this->_pageLayout->getDefaultValue());
        }

        $layoutFieldset->addField(
            'layout_update_xml',
            'textarea',
            [
                'name' => 'layout_update_xml',
                'label' => __('Layout Update XML'),
                'style' => 'height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset = $form->addFieldset(
            'design_fieldset',
            ['legend' => __('Custom Design'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );

        $designFieldset->addField(
            'custom_theme_from',
            'date',
            [
                'name' => 'custom_theme_from',
                'label' => __('Custom Design From'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-from'
            ]
        );

        $designFieldset->addField(
            'custom_theme_to',
            'date',
            [
                'name' => 'custom_theme_to',
                'label' => __('Custom Design To'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled,
                'class' => 'validate-date validate-date-range date-range-custom_theme-to'
            ]
        );

        $options = $this->_labelFactory->create()->getLabelsCollection(__('-- Please Select --'));
        $designFieldset->addField(
            'custom_theme',
            'select',
            [
                'name' => 'custom_theme',
                'label' => __('Custom Theme'),
                'values' => $options,
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset->addField(
            'custom_root_template',
            'select',
            [
                'name' => 'custom_root_template',
                'label' => __('Custom Layout'),
                'values' => $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(true),
                'disabled' => $isElementDisabled
            ]
        );

        $designFieldset->addField(
            'custom_layout_update_xml',
            'textarea',
            [
                'name' => 'custom_layout_update_xml',
                'label' => __('Custom Layout Update XML'),
                'style' => 'height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $metaFieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Meta Data'), 'class' => 'fieldset-wide']
        );

        $metaFieldset->addField(
            'meta_title',
            'textarea',
            [
                'name' => 'meta_title',
                'label' => __('Meta Title'),
                'title' => __('Meta Title'),
                'disabled' => $isElementDisabled
            ]
        );

        $metaFieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Keywords'),
                'title' => __('Meta Keywords'),
                'disabled' => $isElementDisabled
            ]
        );

        $metaFieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Description'),
                'title' => __('Meta Description'),
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_design_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('CMS Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('CMS Page Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
