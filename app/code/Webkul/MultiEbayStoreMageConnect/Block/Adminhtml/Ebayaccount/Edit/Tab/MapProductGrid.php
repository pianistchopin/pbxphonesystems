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
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Model\Config\Source\CategoriesList;

class MapProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var ProductmapRepository
     */
    protected $productmapRepository;

    /**
     * @var CategoriesList
     */
    protected $_categoriesList;

    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Backend\Helper\Data             $backendHelper
     * @param ProductmapRepositoryInterface            $productmapRepository
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        ProductmapRepositoryInterface $productmapRepository,
        CategoriesList $categoriesList,
        array $data = []
    ) {
        $this->_productmapRepository = $productmapRepository;
        $this->_categoriesList = $categoriesList;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ebay_map_product');
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
            $collection = $this->_productmapRepository
                    ->getCollectionByRuleId($id);
        } else {
            $collection = $this->_mapProductCollection->create();
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
            'ebay_pro_id',
            [
                'header' => __('eBay Product Id'),
                'sortable' => true,
                'index' => 'ebay_pro_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'sortable' => true,
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'product_type',
            [
                'header' => __('Type'),
                'sortable' => false,
                'index' => 'product_type'
            ]
        );
        $this->addColumn(
            'magento_pro_id',
            [
                'header' => __('Store Product Id'),
                'sortable' => false,
                'index' => 'magento_pro_id'
            ]
        );
        $this->addColumn(
            'mage_cat_id',
            [
                'header' => __('Store Category'),
                'sortable' => false,
                'index' => 'mage_cat_id',
                'renderer'  => 'Webkul\MultiEbayStoreMageConnect\Block\Adminhtml\Ebayaccount\Edit\Tab\Renderer\MageCategoryName'
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
        $asinCatOptions = $this->_categoriesList->toOptionArray();
        $ruleId = $this->getRequest()->getParam('id');
        $this->setMassactionIdField('entity_id');
        // $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        $this->getMassactionBlock()->setFormFieldName('productEntityIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/MassDelete',
                    [
                        'rule_id'=>$ruleId
                    ]
                ),
                'confirm' => __('Are you sure want to delete?')
            ]
        )->addItem(
            'massassigncate',
            [
                'label'=> __('Assign to category'),
                'url'=> $this->getUrl(
                    '*/*/massassigntocategory',
                    [
                        'rule_id'=>$ruleId
                    ]
                ),
                'additional'=> [
                    'visibility'=> [
                    'name'=> 'magecate',
                    'type'=> 'select',
                    'label'=> __('Assign to category'),
                    'values'=> $asinCatOptions
                    ]
                ]
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
