<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Sales\Model\Config
     */
    private $salesConfig;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $sessionQuote;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    private $catalogConfig;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var array
     */
    private $allowedProductTypes = [
        'simple',
        'configurable',
        'downloadable',
        'virtual',
        'grouped',
        'bundle'
    ];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->sessionQuote = $sessionQuote;
        $this->salesConfig = $salesConfig;
        parent::__construct($context, $backendHelper, $data);
        $this->_template = 'Amasty_RequestQuote::quote/edit/search/grid.phtml';
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_quote_edit_search_grid');
        $this->setRowClickCallback('quote.productGridRowClick.bind(quote)');
        $this->setCheckboxCheckCallback('quote.productGridCheckboxCheck.bind(quote)');
        $this->setRowInitCallback('quote.productGridRowInit.bind(quote)');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->sessionQuote->getStore();
    }

    /**
     * @return  \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $attributes = $this->catalogConfig->getProductAttributes();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productFactory->create()->getCollection();
        $collection->setStore(
            $this->getStore()
        )->addAttributeToSelect(
            $attributes
        )->addAttributeToSelect(
            'sku'
        )->addStoreFilter()->addAttributeToFilter(
            'type_id',
            $this->salesConfig->getAvailableProductTypes()
        )->addAttributeToSelect(
            'gift_message_available'
        );

        $collection->addAttributeToFilter('type_id', $this->allowedProductTypes);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'renderer' => \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search\Grid\Renderer\Product::class,
                'index' => 'name'
            ]
        );
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
                'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
                'index' => 'price',
                'renderer' => \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search\Grid\Renderer\Price::class
            ]
        );

        $this->addColumn(
            'in_products',
            [
                'header' => __('Select'),
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'sortable' => false,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search\Grid\Renderer\Qty::class,
                'name' => 'qty',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $params = ['block' => 'search_grid', '_current' => true, 'collapse' => null];
        if ($quoteId = $this->getRequest()->getParam('quote_id', false)) {
            $params['quote_id'] = $quoteId;
        }
        return $this->getUrl(
            'amasty_quote/quote_edit/loadBlock',
            $params
        );
    }

    /**
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', []);

        return $products;
    }

    /**
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addOptionsToResult();
        return parent::_afterLoadCollection();
    }
}
