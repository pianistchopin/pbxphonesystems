<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Api;

/**
 * @api
 */
interface StemmedWordRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ElasticSearch\Api\Data\StemmedWordInterface $stemmedWord
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function save(\Amasty\ElasticSearch\Api\Data\StemmedWordInterface $stemmedWord);

    /**
     * Get by id
     *
     * @param int $stemmedWord
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($stemmedWord);

    /**
     * Delete
     *
     * @param \Amasty\ElasticSearch\Api\Data\StemmedWordInterface $stemmedWord
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ElasticSearch\Api\Data\StemmedWordInterface $stemmedWord);

    /**
     * Delete by id
     *
     * @param int $stemmedWordId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($stemmedWordId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Stemmed Words array by storeid
     *
     * @param int $storeId
     * @return array
     */
    public function getArrayListByStoreId($storeId);

    /**
     * @param $file
     * @param $storeId
     * @return int
     * @throws \Exception
     */
    public function importStemmedWords($file, $storeId);
}
