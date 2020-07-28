<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder;

use Amasty\ElasticSearch\Api\Data\Indexer\Structure\AnalyzerBuilderInterface;
use Amasty\ElasticSearch\Model\Indexer\Structure\AnalyzerBuilder\EntityCollectionProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Nori
 * @package Amasty\ElasticSearch\Model\Indexer\Structure\AnalyserBuilder
 */
class Nori implements AnalyzerBuilderInterface
{
    /**
     * @var EntityCollectionProvider
     */
    private $entityCollectionProvider;

    /**
     * @var \Amasty\ElasticSearch\Model\Config
     */
    private $config;

    public function __construct(
        EntityCollectionProvider $entityCollectionProvider,
        \Amasty\ElasticSearch\Model\Config $config
    ) {
        $this->entityCollectionProvider = $entityCollectionProvider;
        $this->config = $config;
    }

    /**
     * @param int $storeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build($storeId)
    {
        $filters = $this->getFilters($storeId);
        $defaultFilters = ['lowercase'];
        if ($readingForm = $this->config->getUseNoriReadingForm($storeId)) {
            $defaultFilters[] = 'nori_readingform';
        }
        $analyzerFilters = array_merge($defaultFilters, array_keys($filters));
        $tokenizer = $this->getTokenizer($storeId);

        $analyser = [
            'analyzer' => [
                'default' => [
                    'type'      => 'custom',
                    'tokenizer' => key($tokenizer),
                    'filter'    => $analyzerFilters
                ]
            ],
            'tokenizer' => $tokenizer,
            'filter'   => $filters
        ];

        return $analyser;
    }

    private function getFilters($storeId)
    {
        $filters = [
            'stop_filter' => [
                "type" => "stop",
                "stopwords" => $this->getStopWords($storeId)
            ],
            "synonym" => [
                "type" => "synonym",
                "lenient" => true,
                "synonyms" => $this->getSynonyms($storeId)
            ]
        ];
        if ($stemmingData = $this->getStemmingData($storeId)) {
            if (is_array($stemmingData)) {
                $filters['stemming'] = [
                    'type' => "stemmer_override",
                    'rules' => $stemmingData
                ];
            } else {
                $filters['stemming'] = [
                    'type' => 'kuromoji_stemmer',
                    'minimum_length' => 4
                ];
            }
        }

        return $filters;
    }

    /**
     * @param int $storeId
     * @return array
     */
    private function getTokenizer($storeId)
    {
        return [
            'nori_custom_tokenizer' => [
                'type' => 'nori_tokenizer',
                'mode' => $this->config->getNoriTokenMode($storeId),
            ]
        ];
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
