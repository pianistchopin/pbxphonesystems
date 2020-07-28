<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingArea
 */


namespace Amasty\ShippingArea\Model;

use Amasty\ShippingArea\Api\Data\AreaInterface;
use Amasty\ShippingArea\Api\AreaRepositoryInterface;
use Amasty\ShippingArea\Model\AreaFactory;
use Amasty\ShippingArea\Model\ResourceModel\Area as AreaResource;
use Amasty\ShippingArea\Model\ResourceModel\Area\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class AreaRepository implements AreaRepositoryInterface
{
    /**
     * @var AreaFactory
     */
    private $areaFactory;

    /**
     * @var AreaResource
     */
    private $areaResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $areas;

    public function __construct(
        AreaFactory $areaFactory,
        AreaResource $areaResource,
        CollectionFactory $collectionFactory
    ) {
        $this->areaFactory = $areaFactory;
        $this->areaResource = $areaResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(AreaInterface $area)
    {
        try {
            if ($area->getAreaId()) {
                $area = $this->getById($area->getAreaId())->addData($area->getData());
            }
            $this->areaResource->save($area);
            unset($this->areas[$area->getAreaId()]);
        } catch (\Exception $e) {
            if ($area->getAreaId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save Shipping Area with ID %1. Error: %2',
                        [$area->getAreaId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new Shipping Area. Error: %1', $e->getMessage()));
        }

        return $area;
    }

    /**
     * @inheritdoc
     */
    public function getById($areaId)
    {
        if (!isset($this->areas[$areaId])) {
            /** @var \Amasty\ShippingArea\Model\Area $area */
            $area = $this->areaFactory->create();
            $this->areaResource->load($area, $areaId);
            if (!$area->getAreaId()) {
                throw new NoSuchEntityException(__('Shipping Area with specified ID "%1" not found.', $areaId));
            }
            $this->areas[$areaId] = $area;
        }

        return $this->areas[$areaId];
    }

    /**
     * @inheritdoc
     */
    public function delete(AreaInterface $area)
    {
        try {
            $this->areaResource->delete($area);
            unset($this->areas[$area->getAreaId()]);
        } catch (\Exception $e) {
            if ($area->getAreaId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove Shipping Area with ID %1. Error: %2',
                        [$area->getAreaId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove Shipping Area. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($areaId)
    {
        $areaModel = $this->getById($areaId);
        $this->delete($areaModel);

        return true;
    }

    /**
     * @param int[] $areaIds
     *
     * @return AreaInterface[]
     */
    public function getListByIds($areaIds)
    {
        $idsToLoad = $result = [];
        foreach ($areaIds as $areaId) {
            if (isset($this->areas[$areaId])) {
                $result[$areaId] = $this->areas[$areaId];
            } else {
                $idsToLoad[] = $areaId;
            }
        }
        if (!empty($idsToLoad)) {
            /** @var \Amasty\ShippingArea\Model\ResourceModel\Area\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addActiveFilter()
                ->addFieldToFilter(AreaInterface::AREA_ID, ['in' => $idsToLoad]);

            foreach ($collection->getItems() as $area) {
                $this->areas[$area->getId()] = $area;
                $result[$area->getId()] = $area;
            }
        }

        return $result;
    }
}
