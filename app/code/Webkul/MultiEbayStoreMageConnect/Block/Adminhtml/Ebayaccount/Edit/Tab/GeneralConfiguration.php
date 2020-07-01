<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab;

use \Webkul\MultiEbayStoreMageConnect\Model\Config\Source;

class GeneralConfiguration extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countryList,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSet,
        Source\AllStoreList $allStoreList,
        Source\AllWebsiteList $allWebsiteList,
        Source\CategoriesList $categoriesList,
        Source\ProductImportType $productImportType,
        Source\PriceRuleOption $priceRuleOption,
        \Magento\Sales\Model\Config\Source\Order\Status $orderStatus,
        array $data = []
    ) {
        $this->_attributeSet = $attributeSet;
        $this->storeManager = $context->getStoreManager();
        $this->allStoreList = $allStoreList;
        $this->allWebsiteList = $allWebsiteList;
        $this->categoriesList = $categoriesList;
        $this->orderStatus = $orderStatus;
        $this->productImportType = $productImportType;
        $this->priceRuleOption = $priceRuleOption;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $model = $this->_coreRegistry->registry('ebayaccount_info');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('eBay General Configuration')]);

        if ($model->getId()) {
            $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            if (!$model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $baseFieldset->addField(
            'revise_item',
            'select',
            [
                'label' => __('Revise eBay Product'),
                'title' => __('Revise eBay Product'),
                'required' => true,
                'index' => 'revise_item',
                'name' => 'revise_item',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );


        $baseFieldset->addField(
            'default_cate',
            'select',
            [
                'name' => 'default_cate',
                'label' => __('Default Category'),
                'id' => 'default_cate',
                'title' => __('Default Category'),
                'values' => $this->categoriesList->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );


        $baseFieldset->addField(
            'default_store_view',
            'select',
            [
                'name' => 'default_store_view',
                'label' => __('Default Store View'),
                'id' => 'default_store_view',
                'title' => __('Default Store View'),
                'values' => $this->allStoreList->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'import_product',
            'select',
            [
                'name' => 'import_product',
                'label' => __('Import Product'),
                'id' => 'import_product',
                'title' => __('Import Product'),
                'values' => $this->productImportType->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'item_speci',
            'select',
            [
                'label' => __('Product With Specification'),
                'title' => __('Product With Specification'),
                'required' => true,
                'index' => 'revitem_speciise_item',
                'name' => 'item_speci',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
        $baseFieldset->addField(
            'item_with_html',
            'select',
            [
                'label' => __('Product Description With HTML'),
                'title' => __('Product Description With HTML'),
                'required' => true,
                'index' => 'item_with_html',
                'name' => 'item_with_html',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );

        $baseFieldset->addField(
            'price_rule_on',
            'select',
            [
                'name' => 'price_rule_on',
                'label' => __('Price Rule Applicable For'),
                'id' => 'price_rule_on',
                'title' => __('Price Rule Applicable For'),
                'values' => $this->priceRuleOption->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'order_status',
            'select',
            [
                'name' => 'order_status',
                'label' => __('Order Status'),
                'id' => 'order_status',
                'title' => __('Order Status'),
                'values' => $this->orderStatus->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
