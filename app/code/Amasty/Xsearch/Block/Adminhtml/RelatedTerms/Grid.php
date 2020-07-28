<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Adminhtml\RelatedTerms;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

/**
 * Class Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry = null;

    /**
     * @var \Magento\Search\Model\ResourceModel\Query\CollectionFactory
     */
    private $queryCollectionFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $storeOptions;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Search\Model\ResourceModel\Query\CollectionFactory $queryCollectionFactory,
        \Magento\Store\Model\System\Store $storeOptions,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->storeOptions = $storeOptions;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('search_terms_related');
        $this->setDefaultSort('position');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Search\Model\Query
     */
    public function getTerm()
    {
        return $this->registry->registry('current_catalog_search');
    }

    /**
     * @param Column $column
     * @return $this|Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'related') {
            $termIds = $this->getSelectedTerms();
            if (empty($termIds)) {
                $termIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('query_id', ['in' => $termIds]);
            } elseif (!empty($termIds)) {
                $this->getCollection()->addFieldToFilter('query_id', ['nin' => $termIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->queryCollectionFactory->create();
        $this->setCollection($collection);

        $tableName = $this->getCollection()->getResource()->getTable('amasty_xsearch_related_term');
        $this->getCollection()->getSelect()->joinLeft(
            ['related_terms' => $tableName],
            'related_terms.related_term_id = main_table.query_id AND related_terms.term_id = "' .  $this->getTerm()->getId() . '"',
            ['position' => 'related_terms.position']
        )->group('main_table.query_id');

        $this->getCollection()->addFieldToFilter('query_id', ['neq' => $this->getTerm()->getId()]);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'related',
            [
                'type' => 'checkbox',
                'name' => 'related',
                'values' => $this->getSelectedTerms(),
                'index' => 'query_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'query_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'query_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('query_text', ['header' => __('Search Query'), 'index' => 'query_text']);
        $this->addColumn('num_results', ['header' => __('Results'), 'index' => 'num_results']);
        $this->addColumn('popularity', ['header' => __('Hits'), 'index' => 'popularity']);

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => true,
                'default' => 0
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('amsearch/related/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    private function getSelectedTerms()
    {
        $terms = $this->getRequest()->getPost('selected_terms');
        if ($terms === null) {
            $terms = $this->getTerm()->getRelatedTerms();
            return array_keys($terms);
        }
        return $terms;
    }
}
