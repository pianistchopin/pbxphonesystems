<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\ResourceModel\StemmedWord;

use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Amasty\ElasticSearch\Model\ResourceModel\StemmedWord
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'stemmed_word_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\ElasticSearch\Model\StemmedWord::class,
            \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord::class
        );
    }

    /**
     * @param $ids
     */
    public function deleteByIds($ids)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['stemmed_word_id IN(?)' => implode(',', $ids)]
        );
    }

    /**
     * Add filter by store
     *
     * @param null $store
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        $storeId = $store;
        if ($store instanceof Store) {
            $storeId = $store->getId();
        }
        $this->getSelect()->where('store_id = ?', $storeId);

        return $this;
    }
}
