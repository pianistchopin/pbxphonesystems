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
namespace Ves\PageBuilder\Block\Adminhtml\Blockbuilder\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Group Collection
     */
    protected $_groupCollection;
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ves\PageBuilder\Helper\Data
     */
    protected $_viewHelper;

    protected $_customerGroup;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param \Ves\PageBuilder\Helper\Data           $viewHelper    
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\PageBuilder\Helper\Data $viewHelper,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
        $this->_eventManager->dispatch(
        'ves_check_license',
        ['obj' => $this,'ex'=>'Ves_PageBuilder']
        );
    	/** @var $model \Ves\PageBuilder\Model\Block */
    	$model = $this->_coreRegistry->registry('ves_pagebuilder');
        $wysiwygDescriptionConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Ves_PageBuilder::block_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

        if (($this->hasData('is_valid') && $this->hasData('local_valid')) && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
            $wysiwygDescriptionConfig['enabled'] = $wysiwygDescriptionConfig['add_variables'] = $wysiwygDescriptionConfig['add_widgets'] = $wysiwygDescriptionConfig['add_images'] = 0;
            $wysiwygDescriptionConfig['plugins'] = [];
        }
        $wysiwygDescriptionConfig['add_variables'] = $wysiwygDescriptionConfig['add_widgets'] = 0;
    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('block_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Element Information')]);


    	if ($model->getId()) {
    		$fieldset->addField('block_id', 'hidden', ['name' => 'block_id']);
    	}

    	$fieldset->addField(
    		'title',
    		'text',
    		[
	    		'name' => 'title',
	    		'label' => __('Element Title'),
	    		'title' => __('Element Title'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'alias',
    		'text',
    		[
	    		'name' => 'alias',
	    		'label' => __('URL Key'),
	    		'title' => __('URL Key'),
                'note' => __('Empty to auto create url key'),
	    		'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'shortcode',
            'hidden',
            [
                'name' => 'shortcode',
                'label' => __('Shortcode'),
                'title' => __('Shortcode'),
                'onclick' => 'jQuery(this).select();',
                'disabled' => $isElementDisabled
            ]
            );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField(
            'show_from',
            'date',
            [
                'name' => 'show_from',
                'label' => __('Display Element From Date'),
                'title' => __('Display Element From Date'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'show_to',
            'date',
            [
                'name' => 'show_to',
                'label' => __('Display Element To Date'),
                'title' => __('Display Element To Date'),
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'label' => __('Enable Element for certain customer groups'),
                'title' => __('Enable Element for certain customer groups'),
                'name' => 'customer_group[]',
                'values' => $this->_viewHelper->getCustomerGroups(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'image',
            'text',
            [
                'name' => 'image',
                'label' => __('Image URL'),
                'title' => __('Image URL'),
                'note' => __('Input Image URL to preview the element profile. <br/><strong>For example:</strong> http://domain.com/images/element_profile1.jpg'),
                'disabled' => $isElementDisabled
            ]
            );

        

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'style' => 'height:200px;',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

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
        return __('Element Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Element Information');
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