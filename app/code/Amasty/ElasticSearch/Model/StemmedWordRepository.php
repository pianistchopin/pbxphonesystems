<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model;

use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;
use Amasty\ElasticSearch\Api\StemmedWordRepositoryInterface;
use Amasty\ElasticSearch\Model\ResourceModel\StemmedWord as StemmedWordResource;
use Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory;
use Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\Collection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StemmedWordRepository implements StemmedWordRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var StemmedWordFactory
     */
    private $stemmedWordFactory;

    /**
     * @var StemmedWordResource
     */
    private $stemmedWordResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $stemmedWord;

    /**
     * @var CollectionFactory
     */
    private $stemmedWordCollectionFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csv;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        StemmedWordFactory $stemmedWordFactory,
        StemmedWordResource $stemmedWordResource,
        CollectionFactory $stemmedWordCollectionFactory,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->stemmedWordFactory = $stemmedWordFactory;
        $this->stemmedWordResource = $stemmedWordResource;
        $this->stemmedWordCollectionFactory = $stemmedWordCollectionFactory;
        $this->csv = $csv;
    }

    /**
     * @inheritdoc
     */
    public function save(StemmedWordInterface $stemmedWord)
    {
        try {
            if ($stemmedWord->getStemmedWordId()) {
                $stemmedWord = $this->getById($stemmedWord->getStemmedWordId())->addData($stemmedWord->getData());
            }
            $this->stemmedWordResource->save($stemmedWord);
            unset($this->stemmedWords[$stemmedWord->getStemmedWordId()]);
            // @codingStandardsIgnoreLine
        } catch (AlreadyExistsException $e) {
            throw new AlreadyExistsException(__($e->getMessage()));
        } catch (\Exception $e) {
            if ($stemmedWord->getStemmedWordId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save stemmed word with ID %1. Error: %2',
                        [$stemmedWord->getStemmedWordId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new stemmed word. Error: %1', $e->getMessage()));
        }

        return $stemmedWord;
    }

    /**
     * @inheritdoc
     */
    public function getById($stemmedWordId)
    {
        if (!isset($this->stemmedWords[$stemmedWordId])) {
            /** @var \Amasty\ElasticSearch\Model\StemmedWord $stemmedWord */
            $stemmedWord = $this->stemmedWordFactory->create();
            $this->stemmedWordResource->load($stemmedWord, $stemmedWordId);
            if (!$stemmedWord->getStemmedWordId()) {
                throw new NoSuchEntityException(__('Stemmed Word with specified ID "%1" not found.', $stemmedWordId));
            }
            $this->stemmedWords[$stemmedWordId] = $stemmedWord;
        }

        return $this->stemmedWords[$stemmedWordId];
    }

    /**
     * @inheritdoc
     */
    public function delete(StemmedWordInterface $stemmedWord)
    {
        try {
            $this->stemmedWordResource->delete($stemmedWord);
            unset($this->stemmedWords[$stemmedWord->getStemmedWordId()]);
        } catch (\Exception $e) {
            if ($stemmedWord->getStemmedWordId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove stemmedWord with ID %1. Error: %2',
                        [$stemmedWord->getStemmedWordId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove stemmedWord. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($stemmedWordId)
    {
        $stemmedWordModel = $this->getById($stemmedWordId);
        $this->delete($stemmedWordModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\Collection $stemmedWordCollection */
        $stemmedWordCollection = $this->stemmedWordCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $stemmedWordCollection);
        }
        $searchResults->setTotalCount($stemmedWordCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $stemmedWordCollection);
        }
        $stemmedWordCollection->setCurPage($searchCriteria->getCurrentPage());
        $stemmedWordCollection->setPageSize($searchCriteria->getPageSize());
        $stemmedWords = [];
        /** @var StemmedWordInterface $stemmedWord */
        foreach ($stemmedWordCollection->getItems() as $stemmedWord) {
            $stemmedWords[] = $this->getById($stemmedWord->getId());
        }
        $searchResults->setItems($stemmedWords);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $stemmedWordCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $stemmedWordCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $stemmedWordCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $stemmedWordCollection
     */
    private function addOrderToCollection($sortOrders, Collection $stemmedWordCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $stemmedWordCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getArrayListByStoreId($storeId)
    {
        $result = [];
        /** @var \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\Collection $stemmedWordCollection */
        $stemmedWordCollection = $this->stemmedWordCollectionFactory->create();
        $stemmedWordCollection->addFieldToFilter(StemmedWordInterface::STORE_ID, $storeId);

        /** @var StemmedWordInterface $item */
        foreach ($stemmedWordCollection as $item) {
            $result[] = $item->getTerm();
        }

        return $result;
    }

    /**
     * @param $file
     * @param $storeId
     * @return int
     * @throws \Exception
     */
    public function importStemmedWords($file, $storeId)
    {
        $count = 0;
        $csvData = $this->csv->getData($file);
        foreach ($csvData as $data) {
            if (isset($data[0]) && isset($data[1])) {
                try {
                    $model = $this->stemmedWordFactory
                        ->create()
                        ->setStoreId($storeId)
                        ->setStemmedWord($data[0])
                        ->setWords($data[1]);
                    $this->save($model);
                } catch (AlreadyExistsException $e) {
                    continue;
                } catch (CouldNotSaveException $ex) {
                    continue;
                }
                $count++;
            }
        }

        return $count;
    }
}
