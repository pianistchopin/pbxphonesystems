<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Api\Data;

interface StemmedWordInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const STEMMED_WORD_ID = 'stemmed_word_id';
    const TABLE_NAME = 'amasty_elastic_stemmed_word';
    const STEMMED_WORD = 'stemmed_word';
    const WORDS = 'words';
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getStemmedWordId();

    /**
     * @param int $stemmedWord
     *
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function setStemmedWordId($stemmedWord);

    /**
     * @return string
     */
    public function getStemmedWord();

    /**
     * @param string $stemmedWord
     *
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function setStemmedWord($stemmedWord);

    /**
     * @return string comma separated
     */
    public function getWords();

    /**
     * @param string $words comma separated
     *
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function setWords($words);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function setStoreId($storeId);
}
