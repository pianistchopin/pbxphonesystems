<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Model\Indexer;

use Amasty\Pgrid\Setup\Operation\CreateQtySoldTable;
use Magento\Framework\App\ResourceConnection;
use Amasty\Pgrid\Helper\Data as Helper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class QtySold implements IndexerActionInterface, MviewActionInterface
{
    const INDEXER_ID = 'amasty_pgrid_qty_sold';

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * QtySold constructor.
     * @param ResourceConnection $resource
     * @param Helper $helper
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        ResourceConnection $resource,
        Helper $helper,
        TimezoneInterface $timezone
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->helper = $helper;
        $this->timezone = $timezone;
    }

    /**
     * @return QtySold
     */
    public function executeFull()
    {
        return $this->doReindex();
    }

    /**
     * @param array $ids
     * @return QtySold
     */
    public function executeList(array $ids)
    {
        $select = $this->connection->select();
        $select->from(
            ['sales_order_item' => $this->getTable('sales_order_item')],
            'product_id'
        )->where('order_id IN (?)', $ids);

        return $this->doReindex($this->connection->fetchCol($select));
    }

    /**
     * @param int $id
     * @return QtySold
     */
    public function executeRow($id)
    {
        return $this->executeList([$id]);
    }

    /**
     * @param int[] $ids
     * @return QtySold
     */
    public function execute($ids)
    {
        return $this->executeList($ids);
    }

    /**
     * @param string $tableName
     * @return string
     */
    private function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }

    /**
     * @param array $ids
     * @return $this
     */
    private function doReindex(array $ids = [])
    {
        $fromDate = $this->getDateFrom();
        $toDate = $this->getDateTo();

        $table = $this->getTable(CreateQtySoldTable::TABLE_NAME);
        $where = '';
        if (!empty($ids)) {
            $where = $this->connection->quoteInto('product_id IN (?)', $ids);
        }

        $this->connection->delete($table, $where);

        $columns = [
            'product_id' => 'order_item.product_id',
            'qty_sold' => new \Zend_Db_Expr('SUM(order_item.qty_ordered) - SUM(order_item.qty_refunded)')
        ];

        $select = $this->connection->select();
        $select->from(
            ['sales_order' => $this->getTable('sales_order')],
            $columns
        )->joinInner(
            ['order_item' => $this->getTable('sales_order_item')],
            'order_item.order_id = sales_order.entity_id',
            []
        )->joinLeft(
            ['order_item_parent' => $this->getTable('sales_order_item')],
            'order_item.parent_item_id = order_item_parent.item_id',
            []
        )->group('order_item.product_id');

        $this->addOrderStatuses($select);

        if ($ids) {
            $select->where('order_item.product_id IN (?)', $ids);
        }

        if ($fromDate && $toDate) {
            if (!$toDate) {
                $select->where('sales_order.created_at >= ?' . $fromDate);
            } elseif (!$fromDate) {
                $select->where('sales_order.created_at <= ?' . $toDate);
            } else {
                $select->where(sprintf('sales_order.created_at BETWEEN \'%s\' and \'%s\'', $fromDate, $toDate));
            }
        }

        $query = $this->connection->insertFromSelect($select, $table);
        $this->connection->query($query);

        if (empty($ids)) {
            $addedIds = [];
            $productIds = $this->connection->fetchAll($select);

            foreach ($productIds as $productId) {
                array_push($addedIds, $productId['product_id']);
            }
            $remainedProductsSelect = $this->getRemainedProducts($addedIds);
            $queryRemained = $this->connection->insertFromSelect($remainedProductsSelect, $table);
            $this->connection->query($queryRemained);
        }

        return $this;
    }

    /**
     * @param null|array $entityIds
     *
     * @return Select
     */
    private function getRemainedProducts($entityIds)
    {
        $select = $this->connection->select();

        $select->from(
            ['sales_order' => $this->getTable('catalog_product_entity')],
            [
                'product_id' => 'entity_id',
                'qty_sold' => new \Zend_Db_Expr('0')
            ]
        );

        if ($entityIds) {
            $select->where('entity_id NOT IN (?)', $entityIds);
        }

        return $select;
    }

    /**
     * @return string
     */
    private function getDateFrom()
    {
        return $this->convertDate($this->helper->getModuleConfig('extra_columns/qty_sold_settings/qty_sold_from'));
    }

    /**
     * @return string
     */
    private function getDateTo()
    {
        return $this->convertDate($this->helper->getModuleConfig('extra_columns/qty_sold_settings/qty_sold_to'), true);
    }

    /**
     * @param Select $select
     * @return bool
     */
    private function addOrderStatuses(Select $select)
    {
        $statuses = $this->helper->getModuleConfig('extra_columns/qty_sold_settings/qty_sold_orders');
        if ($statuses) {
            $statuses = explode(',', $statuses);
            $select->where('sales_order.status IN(?)', $statuses);

            return true;
        }

        return false;
    }

    /**
     * Change format from Magento to Mysql
     *
     * @param string $date
     * @param bool $isEnd
     * @return string
     */
    private function convertDate($date, $isEnd = false)
    {
        if (!$date) {
            return '';
        }

        $dateFormat = $this->timezone->getDateFormat();
        $date = $this->timezone->date($date, $dateFormat)->format('Y-m-d');
        if ($isEnd) {
            $date .= ' 23:59:59';
        } else {
            $date .= ' 00:00:00';
        }

        return $date;
    }
}
