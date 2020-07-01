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
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;

class MapOrderGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var OrdermapRepositoryInterface
     */
    protected $_orderMapRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param OrdermapRepositoryInterface             $orderMapRepository
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        OrdermapRepositoryInterface $orderMapRepository,
        array $data = []
    ) {
        $this->_orderMapRepository = $orderMapRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ebay_map_order');
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
            $collection = $this->_orderMapRepository
                    ->getCollectionByRuleId($id);
        } else {
            $collection = $this->_orderMapRepository->create();
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
            'ebay_order_id',
            [
                'header' => __('eBay Order Id'),
                'sortable' => true,
                'index' => 'ebay_order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'mage_order_id',
            [
                'header' => __('Store Order Id'),
                'sortable' => true,
                'index' => 'mage_order_id'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Order Status on eBay'),
                'sortable' => false,
                'index' => 'status'
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'sortable' => false,
                'index' => 'created_at',
                'type' => 'datetime'
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
        $this->getMassactionBlock()->setFormFieldName('orderEntityIds');
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
