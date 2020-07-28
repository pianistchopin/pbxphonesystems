<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model;

use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;

/**
 * Class StemmedWord
 * @package Amasty\ElasticSearch\Model
 */
class StemmedWord extends \Magento\Framework\Model\AbstractModel implements StemmedWordInterface
{
    /**
     * Model Init
     *
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ElasticSearch\Model\ResourceModel\StemmedWord::class);
        $this->setIdFieldName('stemmed_word_id');
    }

    /**
     * @inheritdoc
     */
    public function getStemmedWordId()
    {
        return $this->_getData(StemmedWordInterface::STEMMED_WORD_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStemmedWordId($stemmedWordId)
    {
        $this->setData(StemmedWordInterface::STEMMED_WORD_ID, $stemmedWordId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStemmedWord()
    {
        return $this->_getData(StemmedWordInterface::STEMMED_WORD);
    }

    /**
     * @inheritdoc
     */
    public function setStemmedWord($stemmedWord)
    {
        $this->setData(StemmedWordInterface::STEMMED_WORD, $stemmedWord);

        return $this;
    }

    /**
     * @return string
     */
    public function getWords()
    {
        return $this->getData(StemmedWordInterface::WORDS);
    }

    /**
     * @param string $words
     * @return \Amasty\ElasticSearch\Api\Data\StemmedWordInterface
     */
    public function setWords($words)
    {
        $this->setData(StemmedWordInterface::WORDS, $words);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(StemmedWordInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(StemmedWordInterface::STORE_ID, $storeId);

        return $this;
    }
}
