<?php
/**
 * Google Optimizer Cms Page Tab
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ves\PageBuilder\Block\Adminhtml\Pagebuilder\Edit\Tab;

class Googleoptimizer extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Code
     */
    protected $_codeHelper;

    protected $_analyticsHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Ves\PageBuilder\Helper\Code $codeHelper
     * @param \Ves\PageBuilder\Helper\Data $helperData
     * @param \Magento\GoogleAnalytics\Helper\Data $analyticsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Ves\PageBuilder\Helper\Code $codeHelper,
        \Ves\PageBuilder\Helper\Data $helperData,
        \Magento\GoogleAnalytics\Helper\Data $analyticsHelper,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
        $this->_analyticsHelper = $analyticsHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Get cms page model
     *
     * @return mixed
     * @throws \RuntimeException
     */
    protected function _getEntity()
    {
        $entity = $this->_registry->registry('ves_pagebuilder');
        if (!$entity) {
            throw new \RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }

    /**
     * Get google experiment code model
     *
     * @return \Magento\GoogleOptimizer\Model\Code|null
     */
    protected function _getGoogleExperiment()
    {
        $entity = $this->_getEntity();
        if ($entity->getId()) {
            return $this->_codeHelper->getCodeObjectByEntity($entity);
        }
        return null;
    }


    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Page View Optimization');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Page View Optimization');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
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
        $experimentCodeModel = $this->_getGoogleExperiment();

        $fieldset = $form->addFieldset(
            'googleoptimizer_fields',
            ['legend' => __('Google Analytics Content Experiments Code')]
        );

        $fieldset->addField(
            'experiment_script',
            'textarea',
            [
                'name' => 'experiment_script',
                'label' => __('Experiment Code'),
                'value' => $experimentCodeModel ? $experimentCodeModel->getExperimentScript() : '',
                'class' => 'textarea googleoptimizer',
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => __('Experiment code should be added to the original page only.')
            ]
        );

        $fieldset->addField(
            'code_id',
            'hidden',
            [
                'name' => 'code_id',
                'value' => $experimentCodeModel ? $experimentCodeModel->getCodeId() : '',
                'required' => false
            ]
        );

        $form->setFieldNameSuffix('google_experiment');
        
        $this->setForm($form);

        return parent::_prepareForm();
    }

    
    /**
     * Can show tab in tabs
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->_helperData->isGoogleExperimentEnabled() && $this->_analyticsHelper->isGoogleAnalyticsAvailable();
    }

    /**
     * Tab is hidden
     *
     * @return bool
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
