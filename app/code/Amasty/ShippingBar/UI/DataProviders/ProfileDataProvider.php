<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\DataProviders;

use Amasty\ShippingBar\Api\Data\LabelInterface;
use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Model\Repository\ProfileRepository;
use Amasty\ShippingBar\Model\ResourceModel\Profile\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProfileDataProvider extends AbstractDataProvider
{
    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Modifiers\StoreLabels
     */
    private $storeLabels;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ProfileRepository $profileRepository,
        DataPersistorInterface $dataPersistor,
        Modifiers\StoreLabels $storeLabels,
        ScopeConfigInterface $scopeConfig,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->profileRepository = $profileRepository;
        $this->dataPersistor = $dataPersistor;
        $this->storeLabels = $storeLabels;
        $this->scopeConfig = $scopeConfig;
    }

    public function getData()
    {
        $result = [];

        /** @var \Amasty\ShippingBar\Model\Profile $item */
        foreach ($this->collection->getItems() as $item) {
            $this->profileRepository->addLabelsToProfile($item);
            $result[$item->getId()] = $item->getDataForForm();
        }

        if ($savedData = $this->dataPersistor->get(ProfileInterface::FORM_NAMESPACE)) {
            /** @var ProfileInterface $model */
            $model = $this->collection->getNewEmptyItem();
            $model->setData($savedData);
            $result[$model->getProfileId()] = $model->getDataForForm();
        }

        return $result;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta['general']['children'][ProfileInterface::GOAL]['arguments']['data']['config']['default'] =
            (float)$this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal');

        $meta = array_merge($meta, $this->getLabels());

        return $meta;
    }

    /**
     * Create inputs for store labels
     *
     * @return array
     */
    private function getLabels()
    {
        return [
            'content' => [
                'children' => [
                    LabelInterface::INIT_MESSAGE => [
                        'children' => $this->storeLabels->getStoreInputs(LabelInterface::INIT_MESSAGE)
                    ],
                    LabelInterface::PROGRESS_MESSAGE => [
                        'children' => $this->storeLabels->getStoreInputs(LabelInterface::PROGRESS_MESSAGE)
                    ],
                    LabelInterface::ACHIEVED_MESSAGE => [
                        'children' => $this->storeLabels->getStoreInputs(LabelInterface::ACHIEVED_MESSAGE)
                    ],
                    LabelInterface::TERMS_MESSAGE => [
                        'children' => $this->storeLabels->getStoreInputs(LabelInterface::TERMS_MESSAGE)
                    ],
                ]
            ]
        ];
    }
}
