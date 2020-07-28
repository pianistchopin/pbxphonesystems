<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder;

use Amasty\ElasticSearch\Api\Data\Indexer\Structure\AnalyzerBuilderInterface;
use Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\EntityCollectionProvider;

/**
 * Class Smartcn
 * @package Amasty\ElasticSearch\Model\Indexer\Structure\AnalyserBuilder
 */
class Smartcn implements AnalyzerBuilderInterface
{
    /**
     * @var EntityCollectionProvider
     */
    private $entityCollectionProvider;

    public function __construct(EntityCollectionProvider $entityCollectionProvider)
    {
        $this->entityCollectionProvider = $entityCollectionProvider;
    }

    /**
     * @param int $storeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($storeId)
    {
        $analyser = [
            'analyzer' => [
                'default' => [
                    'type'      => 'custom',
                    'tokenizer' => 'smartcn_tokenizer',
                    'filter'    => [
                        'lowercase',
                        'stop_filter',
                        "synonym"
                    ],
                ]
            ],
            'filter'   => [
                'stop_filter' => [
                    "type" => "stop",
                    "stopwords" => $this->getStopWords($storeId)
                ],
                "synonym" => [
                    "type" => "synonym",
                    "lenient" => true,
                    "synonyms" => $this->getSynonyms($storeId)
                ]
            ],
        ];

        if ($stemmingData = $this->getStemmingData($storeId)) {
            $analyser['filter']['stemming'] = [
                'type' => "stemmer_override",
                'rules' => $stemmingData
            ];

            $analyser['analyzer']['default']['filter'][] = 'stemming';
        }

        return $analyser;
    }

    /**
     * @param $storeId
     * @return array|string
     */
    private function getStopWords($storeId)
    {
        $stopWords = [];
        $collection = $this->entityCollectionProvider->getStopWordCollectionFactory()->create();
        $collection->addStoreFilter($storeId);
        foreach ($collection as $stopWord) {
            $stopWords[] = $stopWord->getTerm();
        }
        if (!count($stopWords)) {
            $stopWords = '_none_';
        }

        return $stopWords;
    }

    /**
     * @param $storeId
     * @return array|string
     */
    private function getStemmingData($storeId)
    {
        $stemmedWords = [];
        $collection = $this->entityCollectionProvider->getStemmedWordCollectionFactory()->create();
        $collection->addStoreFilter($storeId);
        foreach ($collection as $stemmedData) {
            $words = explode(',', $stemmedData->getWords());
            foreach ($words as $word) {
                $stemmedWords[] = sprintf('%s => %s', trim($word), $stemmedData->getStemmedWord());
            }
        }

        return $stemmedWords;
    }

    /**
     * @param $storeId
     * @return array
     */
    private function getSynonyms($storeId)
    {
        $synonyms = [];
        $collection = $this->entityCollectionProvider->getSynonymCollectionFactory()->create();
        $collection->addStoreFilter($storeId);
        foreach ($collection as $synonym) {
            $synonyms[] = $synonym->getTerm();
        }

        return $synonyms ?: ['']; //can't pass empty array to elastic 5.x
    }
}
