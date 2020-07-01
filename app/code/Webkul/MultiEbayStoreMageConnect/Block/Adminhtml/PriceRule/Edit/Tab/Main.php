<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\PriceRule\Edit\Tab;

/**
 * Cms page edit form main tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_countryList;

    /**
     * @var \Webkul\MultiEbayStoreMageConnect\Helper
     */
    private $helper;


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
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->helper = $helper;
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
        $model = $this->_coreRegistry->registry('ebay_pricerule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('eBay Product Price Rule')]);

        if ($model->getEntityId()) {
            $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            if (!$model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $baseFieldset->addField(
            'price_from',
            'text',
            [
                'name' => 'price_from',
                'label' => __('Product price From'),
                'title' => __('Product price From'),
                'required' => true,
                'class' => 'validate-number'
            ]
        );
        $baseFieldset->addField(
            'price_to',
            'text',
            [
                'name' => 'price_to',
                'label' => __('Product price To'),
                'id' => 'price_to',
                'title' => __('Product price To'),
                'required' => true,
                'class' => 'validate-number'
            ]
        );
        // $baseFieldset->addField(
        //     'sku',
        //     'text',
        //     [
        //         'name' => 'sku',
        //         'label' => __('Product SKU Start From'),
        //         'id' => 'sku',
        //         'title' => __('Product SKU Start From'),
        //         // 'required' => true
        //     ]
        // );

        $baseFieldset->addField(
            'operation_type',
            'select',
            [
                'name' => 'operation_type',
                'label' => __('Operation Type'),
                'id' => 'operation_type',
                'title' => __('Operation Type'),
                'values' => $this->helper->getOperationsTypes(),
                'class' => 'required-entry validate-select',
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'operation',
            'select',
            [
                'name' => 'operation',
                'label' => __('Operation'),
                'id' => 'operation',
                'title' => __('Operation'),
                'values' => $this->helper->getOperations(),
                'class' => 'required-entry validate-select',
                'required' => true,
            ]
        );

        $baseFieldset->addField(
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('Price'),
                'id' => 'price',
                'title' => __('Price'),
                'class' => 'required-entry validate-number',
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'ebay_account_id',
            'select',
            [
                'name' => 'ebay_account_id',
                'label' => __('eBay Store'),
                'id' => 'ebay_account_id',
                'title' => __('eBay Store'),
                'values' => $this->helper->getAllEbayStores(),
                'class' => 'required-entry validate-select',
                'required' => true
            ]
        );
        $baseFieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'id' => 'status',
                'title' => __('Status'),
                'values' => $this->helper->getStatus(),
                'required' => true
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
