<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class EbayAccount extends Generic implements TabInterface
{

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Model\Config\Source\EbayGlobalSitesList
     */
    protected $_globalSites;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        Source\EbayGlobalSitesList $globalSites,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSet,
        Source\ListingTemplates $listingTemplates,
        array $data = []
    ) {
        $this->_globalSites = $globalSites;
        $this->_systemStore = $systemStore;
        $this->_attributeSet = $attributeSet;
        $this->listingTemplates = $listingTemplates;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('ebayaccount_info');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('eBay Account'), 'class' => 'fieldset-wide']
        );
        $afterElementHtml = '<p class="nm"><small>' . __('Set unique store name for your account') . '</small></p>';
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'id']);
            $fieldset->addField(
                'store_name',
                'text',
                [
                    'name' => 'store_name',
                    'label' => __('Store Name'),
                    'title' => __('Store Name'),
                    'readonly' => true,
                    'required' => true
                ]
            );
        } else {
            $fieldset->addField(
                'store_name',
                'text',
                [
                    'name' => 'store_name',
                    'label' => __('Store Name'),
                    'title' => __('Store Name'),
                    'required' => true,
                    'after_element_html' => $afterElementHtml
                ]
            );
        }

        $fieldset->addField(
            'attribute_set_id',
            'select',
            [
                'name' => 'attribute_set_id',
                'label' => __('Attribute set Id'),
                'title' => __('Attribute set Id'),
                'required' => true,
                'options' => $this->getAttributeSets()
            ]
        );
        $fieldset->addField(
            'global_site',
            'select',
            [
                'label' => __('Global Site'),
                'title' => __('Global Site'),
                'name' => 'global_site',
                'required' => true,
                'options' => $this->_globalSites->toArray()
            ]
        );
        $fieldset->addField(
            'shop_postal_code',
            'text',
            [
                'label' => __('Postal Code'),
                'title' => __('Postal Code'),
                'name' => 'shop_postal_code',
                'required' => true,
                'after_element_html' => '<button type="button" class = "ebay-authorize">Authorize</button>'
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
        return __('Ebay Account');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Ebay Account');
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

    public function getAttributeSets()
    {
        $attributSetArray = [];
        $attributeSet =  $this->_attributeSet->toOptionArray();
        foreach ($attributeSet as $key => $value) {
            $attributSetArray[$value['value']] = $value['label'];
        }
        return $attributSetArray;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
