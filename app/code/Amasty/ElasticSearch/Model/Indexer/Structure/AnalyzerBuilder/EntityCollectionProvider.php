<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder;

use Amasty\ElasticSearch\Model\ResourceModel\StopWord\CollectionFactory as StopWordCollectionFactory;
use Amasty\ElasticSearch\Model\ResourceModel\Synonym\CollectionFactory as SynoymCollectionFactory;
use Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory as StemmedWordCollectionFactory;

/**
 * Class EntityCollectionProvider
 * @package Amasty\ElasticSearch\Model\Indexer\Structure\AnalyserBuilder
 */
class EntityCollectionProvider
{
    /**
     * @var StopWordCollectionFactory
     */
    private $stopWordCollectionFactory;

    /**
     * @var SynoymCollectionFactory
     */
    private $synoymCollectionFactory;

    /**
     * @var StemmedWordCollectionFactory
     */
    private $stemmedWordCollectionFactory;

    public function __construct(
        StopWordCollectionFactory $stopWordCollectionFactory,
        SynoymCollectionFactory $synoymCollectionFactory,
        StemmedWordCollectionFactory $stemmedWordCollectionFactory
    ) {
        $this->stopWordCollectionFactory = $stopWordCollectionFactory;
        $this->synoymCollectionFactory = $synoymCollectionFactory;
        $this->stemmedWordCollectionFactory = $stemmedWordCollectionFactory;
    }

    /**
     * @return StopWordCollectionFactory
     */
    public function getStopWordCollectionFactory()
    {
        return $this->stopWordCollectionFactory;
    }

    /**
     * @return SynoymCollectionFactory
     */
    public function getSynonymCollectionFactory()
    {
        return $this->synoymCollectionFactory;
    }

    /**
     * @return StemmedWordCollectionFactory
     */
    public function getStemmedWordCollectionFactory()
    {
        return $this->stemmedWordCollectionFactory;
    }
}
