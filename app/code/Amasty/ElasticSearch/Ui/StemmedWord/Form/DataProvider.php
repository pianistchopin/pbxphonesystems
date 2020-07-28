<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Ui\StemmedWord\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;

/**
 * Class DataProvider
 * @package Amasty\ElasticSearch\Ui\StemmedWord\Form
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\ElasticSearch\Model\ResourceModel\StemmedWord\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $item) {
            $data = [
                StemmedWordInterface::STEMMED_WORD_ID => $item->getId(),
                StemmedWordInterface::STEMMED_WORD => $item->getStemmedWord(),
                StemmedWordInterface::WORDS => $item->getWords(),
                StemmedWordInterface::STORE_ID => $item->getStoreId(),
            ];

            $result[$item->getId()] = $data;
        }

        return $result;
    }
}
