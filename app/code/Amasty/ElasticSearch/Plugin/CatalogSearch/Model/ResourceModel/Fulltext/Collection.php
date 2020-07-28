<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Plugin\CatalogSearch\Model\ResourceModel\Fulltext;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as FulltextCollection;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Psr\Log\LoggerInterface;

class Collection
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ToolbarModel
     */
    private $toolbarModel;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ProductMetadataInterface $productMetadata,
        ToolbarModel $toolbarModel,
        MessageManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->productMetadata = $productMetadata;
        $this->toolbarModel = $toolbarModel;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * @param FulltextCollection $collection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(FulltextCollection $collection, $printQuery = false, $logQuery = false)
    {
        if (!$collection->isLoaded() && $this->isLessThan22()) {
            $this->setRelevanceOrder($collection);
        }

        return [$printQuery, $logQuery];
    }

    /**
     * @param FulltextCollection $collection
     */
    public function beforeGetSize(FulltextCollection $collection)
    {
        if (!$collection->isLoaded() && $this->isLessThan22()) {
            $this->setRelevanceOrder($collection);
        }
    }

    private function isLessThan22()
    {
        return version_compare($this->productMetadata->getVersion(), '2.2', '<');
    }

    /**
     * @param FulltextCollection $collection
     * @return FulltextCollection
     */
    private function setRelevanceOrder(FulltextCollection $collection)
    {
        $direction = strtolower($this->toolbarModel->getDirection());
        if ($direction) {
            $collection->setOrder('relevance', $direction);
        } else {
            $collection->setOrder('relevance');
        }

        return $collection;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @return array|mixed
     */
    public function aroundGetFacetedData($subject, \Closure $proceed, $argument)
    {
        try {
            $result = $proceed($argument);
        } catch (StateException $exception) {
            $this->messageManager->addErrorMessage(__('Sorry, search engine is currently unavailable'));
            $this->logger->error($exception->getMessage());
            $result = [];
        }

        return $result;
    }
}
