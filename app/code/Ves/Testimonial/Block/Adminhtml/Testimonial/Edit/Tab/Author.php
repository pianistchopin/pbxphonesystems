<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab;

class Author extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ves\Testimonial\Model\ResourceModel\Testimonial\Collection
     */
    protected $_testimonialCollection;

    protected $_drawLevel;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Testimonial\Model\ResourceModel\Testimonial\Collection $testimonialCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_testimonialCollection = $testimonialCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('testimonial_testimonial');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Testimonial::testimonial_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('testimonial_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

        if ($model->getId()) {
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Nickname'),
                'title' => __('Nickname'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'company_name',
            'text',
            [
                'name' => 'company_name',
                'label' => __('Company Name'),
                'title' => __('Company Name'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'company_website',
            'text',
            [
                'name' => 'company_website',
                'label' => __('Company Website'),
                'title' => __('Company Website'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'job',
            'text',
            [
                'name' => 'job',
                'label' => __('Job'),
                'title' => __('Job'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'linkedin',
            'text',
            [
                'name' => 'linkedin',
                'label' => __('LinkedIn'),
                'title' => __('LinkedIn'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'facebook',
            'text',
            [
                'name' => 'facebook',
                'label' => __('Facebook'),
                'title' => __('Facebook'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'twitter',
            'text',
            [
                'name' => 'twitter',
                'label' => __('Twitter'),
                'title' => __('Twitter'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'youtube',
            'text',
            [
                'name' => 'youtube',
                'label' => __('Youtube'),
                'title' => __('Youtube'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'vimeo',
            'text',
            [
                'name' => 'vimeo',
                'label' => __('Vimeo'),
                'title' => __('Vimeo'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'googleplus',
            'text',
            [
                'name' => 'googleplus',
                'label' => __('Google Plus'),
                'title' => __('Google Plus'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_testimonial_testimonial_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getCatCollection(){
        $model = $this->_coreRegistry->registry('testimonial_testimonial');
        $collection = $this->_categoryCollection
            ->addFieldToFilter('testimonial_id', array('neq' => $model->getId()))
            ->setOrder('position');
        return $collection;
    }

    public function getCats($categories, $cats = [], $level = 0){
        foreach ($cats as $k => $v) {

        }
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Testimonial Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Testimonial Information');
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
