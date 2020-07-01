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
namespace Ves\PageBuilder\Block\Adminhtml\Pagebuilder\Edit\Tab;

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

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Ves_PageBuilder::page_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

        if (($this->hasData('is_valid') && $this->hasData('local_valid')) && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
        }
        
    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('block_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Block Information')]);


    	if ($model->getId()) {
    		$fieldset->addField('block_id', 'hidden', ['name' => 'block_id']);
    	}

    	$fieldset->addField(
    		'title',
    		'text',
    		[
	    		'name' => 'title',
	    		'label' => __('Page Title'),
	    		'title' => __('Page Title'),
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

        /**
         * Check is single store mode
         */
        $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);

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
                'label' => __('Display Block From Date'),
                'title' => __('Display Block From Date'),
                'disabled' => $isElementDisabled,
                'date_format' => $dateFormat
            ]
        );

        $fieldset->addField(
            'show_to',
            'date',
            [
                'name' => 'show_to',
                'label' => __('Display Block To Date'),
                'title' => __('Display Block To Date'),
                'disabled' => $isElementDisabled,
                'date_format' => $dateFormat
            ]
        );

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'label' => __('Enable Block for certain customer groups'),
                'title' => __('Enable Block for certain customer groups'),
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
        return __('Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Page Information');
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