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
 * Class DefaultBulder
 * @package Amasty\ElasticSearch\Model\Indexer\Structure\AnalyserBuilder
 */
class DefaultBuilder implements AnalyzerBuilderInterface
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
        $analyser = [
            'analyzer' => [
                //"the A*b-1^2 O'Neil's" => ["ab12", "oneil"]
                'default' => [
                    'type'      => 'custom',
                    'tokenizer' => 'whitespace',
                    'filter'    => [
                        'lowercase',
                        'stop_filter',
                        "synonym",
                        'word_delimiter'
                    ],
                ],
                //"the A*b-1^2 O'Neil's" => ["a*b-1^2", "o'neil's"]
                'allow_spec_chars' => [
                    'type'      => 'custom',
                    'tokenizer' => 'whitespace',
                    'filter'    => [
                        'lowercase',
                        'stop_filter',
                        'synonym'
                    ],
                ],
            ],
            'filter'   => [
                'word_delimiter' => [
                    // https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-word-delimiter-tokenfilter.html
                    'type'                    => 'word_delimiter',
                    'catenate_all'            => true,
                    'catenate_words'          => false,
                    'catenate_numbers'        => false,
                    //^ catenate all
                    'generate_word_parts'     => false,
                    'generate_number_parts'   => false,
                    'split_on_case_change'    => false,
                    'preserve_original'       => true,
                    'split_on_numerics'       => false,
                ],
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
            if (is_array($stemmingData)) {
                $stemmingData[] = 'stemmer => stemmer';
                $analyser['filter']['stemming'] = [
                    'type' => "stemmer_override",
                    'rules' => $stemmingData
                ];
            } else {
                $analyser['filter']['stemming'] = [
                    'type' => 'stemmer',
                    'language' => $stemmingData
                ];
            }
            $analyser['analyzer']['default']['filter'][] = 'stemming';
            $analyser['analyzer']['allow_spec_chars']['filter'][] = 'stemming';
        }

        return $analyser;
    }

    /**
     * @param $storeId
     * @return array|string
     */
    private function getStopWords($storeId)
    {
        $usePredefined = $this->config->getUsePredefinedStopwords($storeId);
        if ($usePredefined) {
            return sprintf(
                '_%s_',
                $this->config->getStopWordsLanguage($storeId)
            );
        } else {
            $stopWords = [];
            $collection = $this->entityCollectionProvider->getStopWordCollectionFactory()->create();
            $collection->addStoreFilter($storeId);
            foreach ($collection as $stopWord) {
                $stopWords[] = $stopWord->getTerm();
            }
            if (!count($stopWords)) {
                $stopWords = '_none_';
            }
        }

        return $stopWords;
    }

    /**
     * @param $storeId
     * @return array|string
     */
    private function getStemmingData($storeId)
    {
        $usePredefined = $this->config->getUsePredefinedStemming($storeId);
        if ($usePredefined) {
            return $this->config->getStemmedLanguage($storeId);
        } else {
            $stemmedWords = [];
            $collection = $this->entityCollectionProvider->getStemmedWordCollectionFactory()->create();
            $collection->addStoreFilter($storeId);
            foreach ($collection as $stemmedData) {
                $words = explode(',', $stemmedData->getWords());
                foreach ($words as $word) {
                    $stemmedWords[] = sprintf('%s => %s', trim($word), $stemmedData->getStemmedWord());
                }
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
