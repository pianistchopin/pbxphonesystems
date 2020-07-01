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
use Webkul\MultiEbayStoreMageConnect\Model\ProductmapFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MageProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var ProductmapFactory
     */
    protected $_productMap;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Backend\Helper\Data              $backendHelper
     * @param CollectionFactory                         $productCollectionFactory
     * @param ProductmapFactory                         $productMap
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $productCollectionFactory,
        ProductmapFactory $productMap,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Webkul\MultiEbayStoreMageConnect\Helper\Data $helper,
        array $data = []
    ) {
        $this->_productMap = $productMap;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        $this->helper = $helper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mage_map_product');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $mappedProId = [];
        $id = $this->getRequest()->getParam('id');
        $this->helper->getEbayAPI($id);
        $collection = $this->_productMap->create()->getCollection()
                    ->addFieldToFilter('rule_id', $id);
        foreach ($collection as $product) {
            $mappedProId[] = $product->getMagentoProId();
        }

        $mageProCollection = $this->_productCollectionFactory
                            ->create()
                            ->addAttributeToSelect('*')
                            ->addFieldToFilter(
                                'type_id',
                                ['in' => explode(',', $this->helper->config['product_type_allowed'])]
                            );
        if (!empty($mappedProId)) {
            $mageProCollection->addFieldToFilter(
                'entity_id',
                ['nin'=>$mappedProId]
            );
        }
        $mageProCollection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
        $this->setCollection($mageProCollection);
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
                'header' => __('Id'),
                'sortable' => true,
                'index' => 'entity_id',
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
            'type_id',
            [
                'header' => __('Type'),
                'sortable' => true,
                'index' => 'type_id'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'sortable' => false,
                'index' => 'sku'
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
        $this->getMassactionBlock()->setFormFieldName('mageProEntityIds');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Export To eBay'),
                'url' => $this->getUrl(
                    'multiebaystoremageconnect/*/SyncIneBay',
                    [
                        'rule_id'=>$ruleId
                    ]
                )
            ]
        );
        return $this;
    }
}
