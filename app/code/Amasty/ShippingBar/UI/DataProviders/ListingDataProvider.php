<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\DataProviders;

use Amasty\ShippingBar\Model\ResourceModel\Profile\CollectionFactory;
use Amasty\ShippingBar\Api\Data\ProfileInterface;

class ListingDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     *
     * @return \Amasty\ShippingBar\Model\ResourceModel\Profile\Collection
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        /** @var \Amasty\ShippingBar\Model\ResourceModel\Profile\Collection $collection */
        $collection = $this->getCollection();
        switch ($filter->getField()) {
            case ProfileInterface::PAGES:
                $collection->addPagesFilter([$filter->getValue()]);
                break;
            case ProfileInterface::STORES:
                $collection->addStoreFilter($filter->getValue());
                break;
            case ProfileInterface::CUSTOMER_GROUPS:
                $collection->addCustomerGroupFilter($filter->getValue());
                break;
            default:
                parent::addFilter($filter);
        }

        return $collection;
    }
}
