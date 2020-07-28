<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\Repository;

use Amasty\ShippingBar\Api\Data\LabelInterface;
use Amasty\ShippingBar\Api\Data\ProfileInterface;
use Amasty\ShippingBar\Api\ProfileRepositoryInterface;
use Amasty\ShippingBar\Model\ProfileFactory;
use Amasty\ShippingBar\Model\ResourceModel\Label as LabelResource;
use Amasty\ShippingBar\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Amasty\ShippingBar\Model\ResourceModel\Profile as ProfileResource;
use Amasty\ShippingBar\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * @var ProfileFactory
     */
    private $profileFactory;

    /**
     * @var ProfileResource
     */
    private $profileResource;

    /**
     * @var LabelResource
     */
    private $labelResource;

    /**
     * @var ProfileCollectionFactory
     */
    private $profileCollectionFactory;

    /**
     * @var LabelCollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $profiles;

    public function __construct(
        ProfileFactory $profileFactory,
        ProfileResource $profileResource,
        LabelResource $labelResource,
        ProfileCollectionFactory $profileCollectionFactory,
        LabelCollectionFactory $labelCollectionFactory
    ) {
        $this->profileFactory = $profileFactory;
        $this->profileResource = $profileResource;
        $this->labelResource = $labelResource;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->labelCollectionFactory = $labelCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ProfileInterface $profile)
    {
        try {
            if ($profile->getProfileId()) {
                if (!$profile->hasStores()) {
                    $profile->setStores(null);
                }

                if (!$profile->hasCustomerGroups()) {
                    $profile->setCustomerGroups(null);
                }

                if (!$profile->hasPages()) {
                    $profile->setPages(null);
                }

                $profile = $this->getById($profile->getProfileId())->setData($profile->getData());
            }
            $labels = $profile->getLabels();
            $this->profileResource->save($profile->setLabels([]));
            $this->saveLabels($profile->setLabels($labels));
            unset($this->profiles[$profile->getProfileId()]);
        } catch (\Exception $e) {
            if ($profile->getProfileId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save profile with ID %1. Error: %2',
                        [$profile->getProfileId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new profile. Error: %1', $e->getMessage()));
        }

        return $profile;
    }

    /**
     * @param \Amasty\ShippingBar\Model\Profile $profile
     *
     * @return \Amasty\ShippingBar\Model\Profile
     */
    public function addLabelsToProfile($profile)
    {
        if (!$profile->getId()) {
            return $profile;
        }
        /** @var \Amasty\ShippingBar\Model\ResourceModel\Label\Collection $labelCollection */
        $labelCollection = $this->labelCollectionFactory->create();
        $labelCollection->addProfileFilter($profile->getId());
        $labels = [];

        foreach ($labelCollection->getData() as $label) {
            $labels[$label[LabelInterface::ACTION]][$label[LabelInterface::STORE_ID]] =
                $label[LabelInterface::LABEL];
        }

        $profile->setLabels($labels);

        return $profile;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->profiles[$id])) {
            /** @var \Amasty\ShippingBar\Model\Profile $profile */
            $profile = $this->profileFactory->create();
            $this->profileResource->load($profile, $id);
            if (!$profile->getProfileId()) {
                throw new NoSuchEntityException(__('Profile with specified ID "%1" not found.', $id));
            }
            $this->profiles[$id] = $profile;
        }

        return $this->profiles[$id];
    }

    /**
     * @inheritdoc
     */
    public function getLastOne()
    {
        /** @var \Amasty\ShippingBar\Model\ResourceModel\Profile\Collection $collection */
        $collection = $this->profileCollectionFactory->create();

        return $collection->getLastItem();
    }

    /**
     * @inheritdoc
     */
    public function delete(ProfileInterface $profile)
    {
        try {
            $this->profileResource->delete($profile);
            unset($this->profiles[$profile->getProfileId()]);
        } catch (\Exception $e) {
            if ($profile->getProfileId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove profile with ID %1. Error: %2',
                        [$profile->getProfileId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove profile. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $profileModel = $this->getById($id);
        $this->delete($profileModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getByParams($storeViewId, $customerGroup, $page, $position)
    {
        /** @var \Amasty\ShippingBar\Model\ResourceModel\Profile\Collection $collection */
        $collection = $this->profileCollectionFactory->create()->addIsActiveFilter();
        $collection->addStoreFilter($storeViewId)
            ->addCustomerGroupFilter($customerGroup)
            ->addPagesFilter($page)
            ->setOrder(ProfileInterface::PRIORITY, \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC)
            ->setPageSize(1);

        $profile = $collection->getFirstItem();

        if ($profile->isObjectNew() || in_array($profile->getPosition(), $position)) {
            return $profile;
        }

        return $collection->getNewEmptyItem();
    }

    /**
     * @param \Amasty\ShippingBar\Model\Profile $profile
     */
    private function saveLabels($profile)
    {
        $labels = $profile->getLabels() ?: [];
        $profile->setLabels([]);

        /** @var \Amasty\ShippingBar\Model\ResourceModel\Label\Collection $collection */
        $collection = $this->labelCollectionFactory->create();
        $collection->addProfileFilter($profile->getProfileId())->walk('delete');

        foreach ($labels as $action => $storeLabels) {
            foreach ($storeLabels as $storeId => $label) {
                /** @var \Amasty\ShippingBar\Model\Label $model */
                $model = $collection->getNewEmptyItem();
                $model->setProfileId($profile->getProfileId())
                    ->setStoreId($storeId)
                    ->setAction($action)
                    ->setLabel($label);
                $this->labelResource->save($model);
            }
        }
    }
}
