<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Ui\StemmedWord\Listing;

use Magento\Framework\Api\Search\SearchResultInterface;
use Amasty\ElasticSearch\Api\Data\StemmedWordInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as MagentoDataProvider;

/**
 * Class DataProvider
 * @package Amasty\ElasticSearch\Ui\StemmedWord\Listing
 */
class DataProvider extends MagentoDataProvider
{
    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [
            'items'        => [],
            'totalRecords' => $searchResult->getTotalCount(),
        ];

        /** @var StemmedWordInterface $item */
        foreach ($searchResult->getItems() as $item) {
            $data = [
                StemmedWordInterface::STEMMED_WORD_ID => $item->getId(),
                StemmedWordInterface::STEMMED_WORD => $item->getStemmedWord(),
                StemmedWordInterface::WORDS => $item->getWords(),
                StemmedWordInterface::STORE_ID => $item->getStoreId(),
            ];

            $result['items'][] = $data;
        }

        return $result;
    }
}
