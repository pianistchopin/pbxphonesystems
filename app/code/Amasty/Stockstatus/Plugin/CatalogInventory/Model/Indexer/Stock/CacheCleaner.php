<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\CatalogInventory\Model\Indexer\Stock;

use Magento\CatalogInventory\Model\Indexer\Stock\CacheCleaner as NativeCacheCleaner;
use Magento\Catalog\Model\Product;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class CacheCleaner
{
    /**
     * @var array
     */
    private $productIds;

    /**
     * @var array
     */
    private $productStatusesBefore;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    public function __construct(
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        ResourceConnection $resourceConnection,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->resourceConnection = $resourceConnection;
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * @param NativeCacheCleaner $subject
     * @param array $productIds
     * @param callable $reindex
     */
    public function beforeClean($subject, $productIds, $reindex)
    {
        $this->productIds = $productIds;
        $this->productStatusesBefore = $this->getProductStockStatuses($this->productIds);
    }

    /**
     * @param NativeCacheCleaner $subject
     */
    public function afterClean($subject)
    {
        $productStatusesAfter = $this->getProductStockStatuses($this->productIds);
        $commonProductsIds = array_intersect(array_keys($this->productStatusesBefore), array_keys($productStatusesAfter));
        $productIds = [];

        foreach ($commonProductsIds as $productId) {
            $statusBefore = $this->productStatusesBefore[$productId];
            $statusAfter = $productStatusesAfter[$productId];

            if ($statusAfter['qty'] != $statusBefore['qty']) {
                $productIds[] = $productId;
            }
        }

        if (!empty($productIds)) {
            $this->cacheContext->registerEntities(Product::CACHE_TAG, $productIds);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        }
    }

    /**
     * @param array $productIds
     * @return array
     */
    private function getProductStockStatuses($productIds)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->resourceConnection->getTableName('cataloginventory_stock_status'),
                ['product_id', 'stock_status', 'qty']
            )->where('product_id IN (?)', $productIds)
            ->where('stock_id = ?', Stock::DEFAULT_STOCK_ID)
            ->where('website_id = ?', $this->stockConfiguration->getDefaultScopeId());

        $statuses = [];
        foreach ($this->getConnection()->fetchAll($select) as $item) {
            $statuses[$item['product_id']] = $item;
        }

        return $statuses;
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
