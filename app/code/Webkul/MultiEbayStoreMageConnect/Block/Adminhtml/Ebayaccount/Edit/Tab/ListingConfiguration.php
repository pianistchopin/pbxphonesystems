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

class ListingConfiguration extends \Magento\Backend\Block\Widget\Form\Generic
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
        Source\ListingTemplates $listingTemplates,
        Source\ListingDuration $listingDuration,
        Source\ProductType $productType,
        Source\AlleBayShipping $ebayShipping,
        Source\DispatchTime $dispatchTime,
        Source\ReturnPolicyList $returnPolicy,
        Source\ReturnDaysList $returnDays,
        Source\PayBy $payBy,
        array $data = []
    ) {
        $this->listingTemplates = $listingTemplates;
        $this->listingDuration = $listingDuration;
        $this->productType = $productType;
        $this->storeManager = $context->getStoreManager();
        $this->ebayShipping = $ebayShipping;
        $this->dispatchTime = $dispatchTime;
        $this->returnPolicy = $returnPolicy;
        $this->returnDays = $returnDays;
        $this->payBy = $payBy;
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

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('eBay Listing Configuration')]);

        if ($model->getId()) {
            $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            if (!$model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $qtyHint = '<small>'.__('Default qty will be used in case qty is 0 while export').'</small>';
        $baseFieldset->addField(
            'default_qty',
            'text',
            [
                'name' => 'default_qty',
                'label' => __('Default Product Quantity'),
                'title' => __('Default Product Quantity'),
                'required' => true,
                'after_element_html' => $qtyHint
            ]
        );

        $tmpHint = '<small>'.__('Selected template will be used for product description').'</small>';
        $baseFieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'template_id',
                'label' => __('Select Template'),
                'id' => 'template_id',
                'title' => __('Select Template'),
                'values' => $this->listingTemplates->toOptionArray(),
                'after_element_html' => $tmpHint
            ]
        );

        $productTypeHint = '<small>'.__('Selected product types product you can export').'</small>';    $baseFieldset->addField(
            'product_type_allowed',
            'multiselect',
            [
                'name' => 'product_type_allowed',
                'label' => __('Product Type For Export'),
                'id' => 'product_type_allowed',
                'title' => __('Product Type For Export'),
                'values' => $this->productType->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
                'after_element_html' => $productTypeHint
            ]
        );

        $baseFieldset->addField(
            'listing_duration',
            'select',
            [
                'name' => 'listing_duration',
                'label' => __('Listing Duration'),
                'id' => 'listing_duration',
                'title' => __('Listing Duration'),
                'values' => $this->listingDuration->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $baseFieldset->addField(
            'dispatch_time',
            'select',
            [
                'name' => 'dispatch_time',
                'label' => __('Dispatch Time'),
                'id' => 'dispatch_time',
                'title' => __('Dispatch Time'),
                'values' => $this->dispatchTime->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'paypal_id',
            'text',
            [
                'name' => 'paypal_id',
                'label' => __('Paypal Email Address'),
                'title' => __('Paypal Email Address'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'ship_free',
            'select',
            [
                'label' => __('Shipping Free'),
                'title' => __('Shipping Free'),
                'required' => true,
                'index' => 'ship_free',
                'name' => 'ship_free',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
        $baseFieldset->addField(
            'shipping_service',
            'select',
            [
                'name' => 'shipping_service',
                'label' => __('Shipping Service'),
                'id' => 'shipping_service',
                'title' => __('Shipping Service'),
                'values' => $this->ebayShipping->toOptionArray(),
                'class' => 'required-entry',
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'ship_cost',
            'text',
            [
                'name' => 'ship_cost',
                'label' => __('Shiping Cost'),
                'title' => __('Shiping Cost'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'ship_aditional_cost',
            'text',
            [
                'name' => 'ship_aditional_cost',
                'label' => __('Shipping Service Additional Cost'),
                'title' => __('Shipping Service Additional Cost'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'ship_priority',
            'text',
            [
                'name' => 'ship_priority',
                'label' => __('Shipping Service Priority'),
                'title' => __('Shipping Service Priority'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'ship_min_time',
            'text',
            [
                'name' => 'ship_min_time',
                'label' => __('Shipping Time Min'),
                'title' => __('Shipping Time Min'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'ship_max_time',
            'text',
            [
                'name' => 'ship_max_time',
                'label' => __('Shipping Time Max'),
                'title' => __('Shipping Time Max'),
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'return_policy',
            'select',
            [
                'name' => 'return_policy',
                'label' => __('Define Return Policy'),
                'id' => 'return_policy',
                'title' => __('Define Return Policy'),
                'values' => $this->returnPolicy->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'returns_within',
            'select',
            [
                'name' => 'returns_within',
                'label' => __('Return Within'),
                'id' => 'returns_within',
                'title' => __('Return Within'),
                'values' => $this->returnDays->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'pay_by',
            'select',
            [
                'name' => 'pay_by',
                'label' => __('Pay By'),
                'id' => 'pay_by',
                'title' => __('Pay By'),
                'values' => $this->payBy->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $baseFieldset->addField(
            'other_info',
            'textarea',
            [
                'name' => 'other_info',
                'label' => __('Other Information'),
                'id' => 'other_info',
                'title' => __('Other Information'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
