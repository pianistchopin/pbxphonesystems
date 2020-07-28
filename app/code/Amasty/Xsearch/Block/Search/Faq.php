<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Faq extends AbstractSearch
{
    const FAQ_BLOCK_PAGE = 'faq';

    /**
     * @var AbstractCollection
     */
    private $categoriesSearchCollection;

    /**
     * @var AbstractCollection
     */
    private $questionsSearchCollection;

    /**
     * @return string
     */
    public function getBlockType()
    {
        return self::FAQ_BLOCK_PAGE;
    }

    /**
     * @return AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function generateCollection()
    {
        $collection = parent::generateCollection();

        foreach ($this->getCategoriesCollection() as $item) {
            $item->setUrl($item->getRelativeUrl());
            $this->addToFaqCollection($item, $collection);
        }

        foreach ($this->getQuestionsCollection() as $item) {
            $item->setUrl($item->getRelativeUrl());
            $this->addToFaqCollection($item, $collection);
        }

        return $collection;
    }

    /**
     * @return array[]
     */
    public function getResults()
    {
        $result = parent::getResults();
        foreach ($this->getSearchCollection() as $index => $item) {
            $result[$index]['short_answer'] = $item->getShortAnswer();
            $result[$index]['answer'] = $item->getAnswer();
        }

        return $result;
    }

    /**
     * @param $item
     * @param $collection
     */
    private function addToFaqCollection($item, &$collection)
    {
        $dataObject = $this->getData('dataObjectFactory')->create();
        $dataObject->setData($item->getData());
        $collection->addItem($dataObject);
    }

    /**
     * @return AbstractCollection
     */
    private function getCategoriesCollection()
    {
        if ($this->categoriesSearchCollection === null) {
            $this->categoriesSearchCollection = $this->getData('categoriesCollectionFactory')->create()
                ->addSearchFilter($this->getQuery()->getQueryText())
                ->addFieldToFilter('status', 1);
        }

        return $this->categoriesSearchCollection;
    }

    /**
     * @return AbstractCollection
     */
    private function getQuestionsCollection()
    {
        if ($this->questionsSearchCollection === null) {
            $this->questionsSearchCollection = $this->getData('questionsCollectionFactory')->create()
                ->addSearchFilter($this->getQuery()->getQueryText())
                ->addFieldToFilter('visibility', 1)
                ->addFieldToFilter('status', 1);
        }

        return $this->questionsSearchCollection;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getSearchUrl(\Magento\Framework\DataObject $item)
    {
        return $item->getUrl();
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getName(\Magento\Framework\DataObject $item)
    {
        return $this->generateName($item->getTitle());
    }

    /**
     * @inheritdoc
     */
    public function getDescription(\Magento\Framework\DataObject $item)
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function getIndexFulltextValues()
    {
        $faqValues = $this->questionsSearchCollection->getIndexFulltextValues();
        $categoryValues = $this->categoriesSearchCollection->getIndexFulltextValues();

        return array_merge($categoryValues, $faqValues);
    }
}
