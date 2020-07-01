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

/**
 * Customer account form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Style extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
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
        if ($this->_isAllowedAction('Ves_PageBuilder::page_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        if (($this->hasData('is_valid') && $this->hasData('local_valid')) && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('block_');

        $model = $this->_coreRegistry->registry('ves_pagebuilder');

        $fieldset = $form->addFieldset(
            'css_setting',
            ['legend' => __('Custom Style / JS'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'custom_css',
            'textarea',
            [
                'name' => 'custom_css',
                'label' => __('Custom CSS'),
                'title' => __('Custom CSS'),
                'note' => __('Enter custom CSS code here. Your custom CSS will be outputted only on this particular page.'),
                'style'     => 'width:90%;height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'custom_js',
            'textarea',
            [
                'name' => 'custom_js',
                'label' => __('Custom JS'),
                'title' => __('Custom JS'),
                'note' => __('Enter custom JS code here. Your custom JS will be outputted only on this particular page.'),
                'style'     => 'width:90%;height:24em;',
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
        return __('Custom Style / JS');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Custom Style / JS');
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
