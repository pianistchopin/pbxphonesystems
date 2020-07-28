<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Ui\DataProviders;

use Amasty\ShippingArea\Api\Data\AreaInterface;
use Amasty\ShippingArea\Model\ResourceModel\Area\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class AreaDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    public function getData()
    {
        $result = [];

        /** @var \Amasty\ShippingArea\Model\Area $item */
        foreach ($this->collection->getItems() as $item) {
            // prepare data
            $item->getPostcodeSet();
            $item->getCountrySet();
            $item->getStateSetListing();
            $result[$item->getId()] = $item->getData();
        }

        if ($savedData = $this->dataPersistor->get(AreaInterface::FORM_NAMESPACE)) {
            /** @var AreaInterface $model */
            $model = $this->collection->getNewEmptyItem();
            $model->setData($savedData);
            $model->getPostcodeSet();
            $model->getCountrySet();
            $model->getStateSetListing();
            $result[$model->getAreaId()] = $model->getData();
        }

        return $result;
    }
}
