<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Plugin\Catalog\Ui\DataProvider\Product;

use Amasty\Pgrid\Setup\Operation\CreateQtySoldTable;
use Amasty\Pgrid\Api\Data\QtySoldInterface;
use Magento\Eav\Model\Entity as EavEntity;
use Amasty\Pgrid\Ui\Component\Listing\Column\Availability;
use Magento\CatalogInventory\Api\StockConfigurationInterface as StockConfigurationInterface;

class ProductDataProvider
{
    /**
     * @var array
     */
    protected $_columns = [
        'amasty_categories',
        'amasty_link',
        'amasty_availability',
        'amasty_created_at',
        'amasty_updated_at',
        'amasty_related_products',
        'amasty_up_sells',
        'amasty_cross_sells',
        'amasty_low_stock'
    ];

    /**
     * @var array
     */
    protected $visibleColumns = ['price', 'qty'];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryColFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_categoriesPath;

    /**
     * @var \Magento\Ui\Api\BookmarkManagementInterface
     */
    protected $_bookmarkManagement;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_http;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var Availability
     */
    protected $availabilityColumn;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Framework\Escaper $escaper,
        \Amasty\Pgrid\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $http,
        Availability $availabilityColumn,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->_categoryColFactory = $categoryColFactory;
        $this->_url = $url;
        $this->_bookmarkManagement = $bookmarkManagement;
        $this->_helper = $helper;
        $this->_http = $http;
        $this->availabilityColumn = $availabilityColumn;
        $this->stockConfiguration = $stockConfiguration;

        $request = $this->_http->getParams();
        if (isset($request['data'])) {
            $data = json_decode($request['data'], true);
            if (isset($data['column'])) {
                $this->visibleColumns[] = $data['column'];
            }
        }
        $this->escaper = $escaper;
    }

    protected function _getCategories($row)
    {
        $categoriesHtml = '';
        $categories = $row->getCategoryCollection()->addNameToResult();
        if ($categories) {
            foreach ($categories as $category) {
                $path = '';
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path .= $categories[$categoryId]->getName() . '/';
                    }
                }

                if ($path) {
                    $path = substr($path, 0, -1);
                    $path =
                        '<div style="font-size: 90%; margin-bottom: 8px; border-bottom: 1px dotted #bcbcbc;">' . $path
                        . '</div>';
                }

                $categoriesHtml .= $path;
            }
        }

        return $categoriesHtml;
    }

    private function prepareColumns($columns)
    {
        foreach ($columns as $key => $column) {
            if (isset($column['visible']) && $column['visible']) {
                $this->visibleColumns[] = $key;
            }
        }
    }

    protected function getVisibleColumns()
    {
        $bookmarks = $this->_bookmarkManagement->loadByNamespace('product_listing');

        /** @var \Magento\Ui\Api\Data\BookmarkInterface $bookmark */
        foreach ($bookmarks->getItems() as $bookmark) {
            if (isset($bookmark->getConfig()['current']['columns'])) {
                $columns = $bookmark->getConfig()['current']['columns'];
                $this->prepareColumns($columns);
            } elseif (isset($bookmark->getConfig()['views'][$bookmark->getIdentifier()]['data']['columns'])) {
                $columns = $bookmark->getConfig()['views'][$bookmark->getIdentifier()]['data']['columns'];
                $this->prepareColumns($columns);
            }
        }

        return array_unique($this->visibleColumns);
    }

    public function beforeGetData(\Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject)
    {
        $visibleColumns = $this->getVisibleColumns();
        $request = $this->_http->getParams();
        if (isset($request['sorting']['field']) || isset($request['sorting']['field'])) {
            $sortingField = $this->escaper->escapeHtml($request['sorting']['field']);
            $sortingDirection = $this->escaper->escapeHtml($request['sorting']['direction']);

            switch ($sortingField) {
                case 'amasty_updated_at':
                    $subject->getCollection()->setOrder('updated_at', $sortingDirection);
                    break;
                case 'amasty_created_at':
                    $subject->getCollection()->setOrder('created_at', $sortingDirection);
                    break;
            }
        }

        foreach ($visibleColumns as $column) {
            $subject->getCollection()->addFieldToSelect($column);
        }

        if (in_array('amasty_categories', $visibleColumns)
            || in_array('amasty_link', $visibleColumns)
        ) {
            $subject->getCollection()->addUrlRewrite();
        }

        if (in_array('amasty_availability', $visibleColumns)
            && !$subject->getCollection()->getFlag('amasty_instock_filter')
        ) {
            $this->addInventoryColumn(
                $subject->getCollection(),
                'amasty_availability',
                $this->availabilityColumn->getAvailableExpression()
            );
        }

        if (in_array('amasty_backorders', $visibleColumns)) {
            $this->addInventoryColumn($subject->getCollection(), 'amasty_backorders', 'backorders');
        }

        if (in_array('amasty_low_stock', $visibleColumns)) {
            $this->_addLowStock($subject->getCollection());
        }

        if (in_array('amasty_qty_sold', $visibleColumns)) {
            $this->addQtySoldColumn($subject->getCollection());
        }
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    private function addQtySoldColumn($collection)
    {
        $collection->joinField(
            'amasty_qty_sold',
            CreateQtySoldTable::TABLE_NAME,
            QtySoldInterface::QTY_SOLD,
            QtySoldInterface::PRODUCT_ID . '=' . EavEntity::DEFAULT_ENTITY_ID_FIELD,
            null,
            'left'
        );
    }

    /**
     * Added amasty column to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string                                                  $amastyColumnName
     * @param string                                                  $columnName
     */
    private function addInventoryColumn($collection, $amastyColumnName, $columnName)
    {
        $collection->joinField(
            $amastyColumnName,
            'cataloginventory_stock_item',
            $columnName,
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
    }

    protected function _addLowStock($collection)
    {
        $configManageStock = $this->stockConfiguration->getManageStock();

        $globalNotifyStockQty = (float)$this->_helper->getScopeValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_NOTIFY_STOCK_QTY
        );

        $stockItemWhere = '({{table}}.low_stock_date is not null) '
            . " AND ( ({{table}}.use_config_manage_stock=1 AND {$configManageStock}=1)"
            . " AND {{table}}.qty < "
            . "IF(amasty_low_stock_item.`use_config_notify_stock_qty`,"
            . " {$globalNotifyStockQty}, {{table}}.notify_stock_qty)"
            . ' OR ({{table}}.use_config_manage_stock=0 AND {{table}}.manage_stock=1) )';

        $collection
            ->addAttributeToSelect('name', true)
            ->joinTable(
                ['amasty_low_stock_item' => 'cataloginventory_stock_item'],
                'product_id=entity_id',
                ['if(amasty_low_stock_item.item_id IS NULL, 0 , 1) as amasty_low_stock'],
                $stockItemWhere,
                'left'
            )
            ->setOrder('amasty_low_stock_item.low_stock_date');
    }

    protected function _isColumnVisible($bookmark, $column)
    {
        return (isset($bookmark['current']['columns'])
            && isset($bookmark['current']['columns'][$column])
            && isset($bookmark['current']['columns'][$column]['visible'])
            && $bookmark['current']['columns'][$column]['visible']) || in_array($column, $this->visibleColumns);
    }

    protected function _initCategories($collection, &$result)
    {
        $idx = 0;

        foreach ($collection as $product) {

            $amastyCategories = null;

            if (isset($result['items']) && isset($result['items'][$idx])) {
                $amastyCategories = $this->_getCategories($product);
            }

            $result['items'][$idx]['amasty_categories'] = $amastyCategories;
            $idx++;
        }
    }

    protected function _initExtra(&$row, $column)
    {
        switch ($column) {
            case "amasty_link":
                if (isset($row['request_path'])) {
                    $row[$column] = $this->_url->getUrl('', ['_direct' => $row['request_path']]);
                } else {
                    $row[$column] = $this->_url->getUrl(
                        null,
                        ['_direct' => 'catalog/product/view/id/' . $row['entity_id']]
                    );
                }
                break;
            case "amasty_created_at":
                $row[$column] = $row['created_at'];
                break;
            case "amasty_updated_at":
                $row[$column] = $row['updated_at'];
                break;
        }
    }

    protected function _initRelatedProducts($productsCollection, $column, &$result)
    {
        $idx = 0;

        foreach ($productsCollection as $product) {
            $ret = '';
            $collection = null;

            switch ($column) {
                case "amasty_related_products":
                    $collection = $product->getRelatedProductCollection();
                    break;
                case "amasty_up_sells":
                    $collection = $product->getUpSellProductCollection();
                    break;
                case "amasty_cross_sells":
                    $collection = $product->getCrossSellProductCollection();
                    break;
            }

            $qty = $this->_helper->getModuleConfig('extra_columns/product_settings/products_qty');

            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'left');
            $collection->setPageSize($qty);

            $items = $collection->getItems();

            if ($items) {

                foreach ($collection->getItems() as $item) {
                    $ret .= '<div style="font-size: 90%; margin-bottom: 8px; border-bottom: 1px dotted #bcbcbc;">'
                        . $item->getName() . '</div>';
                }
            }

            $result['items'][$idx][$column] = $ret;
            $idx++;
        }
    }

    public function afterGetData(
        \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject,
        $result
    ) {
        $columns = $this->getVisibleColumns();

        foreach ($columns as $column) {
            switch ($column) {
                case "amasty_categories":
                    $this->_initCategories($subject->getCollection(), $result);
                    break;
                case "amasty_related_products":
                case "amasty_up_sells":
                case "amasty_cross_sells":
                    $this->_initRelatedProducts($subject->getCollection(), $column, $result);
                    break;
                case "price":
                    $this->processPriceColumn($result);
                    break;
                case "qty":
                    $this->processQtyColumn($result);
                    break;
                default:
                    $this->processExtraColumn($result, $column);
                    break;
            }
        }

        return $result;
    }

    /**
     * @param array $result
     */
    protected function processQtyColumn(&$result)
    {
        if (isset($result['items'])) {
            foreach ($result['items'] as $idx => $item) {
                if (isset($item['qty'])) {
                    if ($this->_helper->getModuleConfig('modification/show_integer')) {
                        $result['items'][$idx]['qty'] = (int)$item['qty'];
                    }
                }
            }
        }
    }

    /**
     * @param array $result
     */
    protected function processPriceColumn(&$result)
    {
        if (isset($result['items'])) {
            foreach ($result['items'] as $idx => $item) {
                if (isset($item['price'])) {
                    $result['items'][$idx]['amasty_price'] = $item['price'];
                }
            }
        }
    }

    /**
     * @param array $result
     */
    protected function processExtraColumn(&$result, $column)
    {
        if (isset($result['items'])) {
            foreach ($result['items'] as $idx => $item) {
                $this->_initExtra($result['items'][$idx], $column);
            }
        }
    }
}
