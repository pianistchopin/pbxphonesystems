<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Api;

/**
 * @api
 */
interface ProfileRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ShippingBar\Api\Data\ProfileInterface $profile
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function save(Data\ProfileInterface $profile);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Return last entity created or empty one if there are no ones.
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function getLastOne();

    /**
     * Delete
     *
     * @param Data\ProfileInterface $profile
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\ProfileInterface $profile);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * @param int $storeViewId
     * @param int $customerGroup
     * @param string $page
     * @param array $position
     *
     * @return Data\ProfileInterface
     */
    public function getByParams($storeViewId, $customerGroup, $page, $position);

    /**
     * @param Data\ProfileInterface $profile
     *
     * @return Data\ProfileInterface
     */
    public function addLabelsToProfile($profile);
}
