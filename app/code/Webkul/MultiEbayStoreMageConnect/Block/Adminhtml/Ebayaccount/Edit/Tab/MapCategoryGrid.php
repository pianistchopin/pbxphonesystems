<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategorymapRepositoryInterface;

class MapCategoryGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var EbaycategorymapRepositoryInterface
     */
    protected $_ebayCategoryMapRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param EbaycategorymapRepositoryInterface      $ebayCategoryMapRepository
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        EbaycategorymapRepositoryInterface $ebayCategoryMapRepository,
        array $data = []
    ) {
        $this->_ebayCategoryMapRepository = $ebayCategoryMapRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ebay_map_category');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $id =  $this->getRequest()->getParam('id');
        if ($id) {
            $collection = $this->_ebayCategoryMapRepository
                    ->getCollectionByRuleId($id);
        } else {
            $collection = $this->_ebayCategoryMapRepository->create();
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Entity Id'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'mage_cat_id',
            [
                'header' => __('Store Category'),
                'sortable' => true,
                'index' => 'mage_cat_id',
                'renderer'  => 'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\Renderer\MageCategoryName'

            ]
        );
        $this->addColumn(
            'ebay_cat_id',
            [
                'header' => __('Ebay Category'),
                'sortable' => true,
                'filter' => false,
                'index' => 'ebay_cat_id'
            ]
        );
        $this->addColumn(
            'ebay_cat_name',
            [
                'header' => __('Ebay Category Name'),
                'sortable' => true,
                'filter' => false,
                'index' => 'ebay_cat_name'
            ]
        );
        $this->addColumn(
            'pro_condition_attr',
            [
                'header' => __('Condition Attribute'),
                'sortable' => true,
                'filter' => false,
                'index' => 'pro_condition_attr'
            ]
        );
        $this->addColumn(
            'variations_enabled',
            [
                'header' => __('Variation'),
                'sortable' => true,
                'filter' => false,
                'index' => 'variations_enabled'
            ]
        );
        $this->addColumn(
            'ean_status',
            [
                'header' => __('EAN Status'),
                'sortable' => true,
                'filter' => false,
                'index' => 'ean_status'
            ]
        );
        $this->addColumn(
            'upc_status',
            [
                'header' => __('UPC Status'),
                'sortable' => true,
                'filter' => false,
                'index' => 'upc_status'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * get massaction
     * @return object
     */
    protected function _prepareMassaction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $this->setMassactionIdField('entity_id');
        $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        $this->getMassactionBlock()->setFormFieldName('cateEntityIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    'multiebaystoremageconnect/*/MassDelete',
                    [
                        'rule_id'=>$ruleId
                    ]
                ),
                'confirm' => __('Are you sure want to delete?')
            ]
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('multiebaystoremageconnect/*/resetgrid', ['_current' => true]);
    }
}
