<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Templates\Edit;

/**
 * Adminhtml MultiEbayStoreMageConnect Map Category Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * \Magento\Cms\Model\Wysiwyg\Config
     */
    private $wysiwygConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /** Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $listingTemplates = $this->_coreRegistry->registry('listing_templates');
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );
        $form->setHtmlIdPrefix('listing_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Listing Template'), 'class' => 'fieldset-wide']
        );

        if ($listingTemplates->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'template_title',
            'text',
            [
                'name' => 'template_title',
                'label' => __('Title'),
                'id' => 'template_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $configuration = $this->wysiwygConfig->getConfig();
        $configuration->setAddVariables(0);
        $configuration->setAddWidgets(0);
        $fieldset->addField(
            'template_content',
            'editor',
            [
                'name' => 'template_content',
                'label' => __('Template Content'),
                'id' => 'template_content',
                'config' => $configuration,
                'wysiwyg' => true,
                'title' => __('Template Content'),
                'class' => 'required-entry',
                'required' => true,
                'style' => 'height: 600px;'
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'id' => 'status',
                'title' => __('Status'),
                'class' => 'required-entry',
                'values' => [['label' => __('Enable'), 'value'=> 1], ['label' => __('Disable'), 'value'=> 2]],
                'required' => true,
            ]
        );

        $form->setUseContainer(true);
        $form->setValues($listingTemplates->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
