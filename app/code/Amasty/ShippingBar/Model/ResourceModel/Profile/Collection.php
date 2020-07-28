<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\ResourceModel\Profile;

use Amasty\ShippingBar\Api\Data\ProfileInterface;

/**
 * @method \Amasty\ShippingBar\Model\Profile|ProfileInterface getFirstItem()
 * @method \Amasty\ShippingBar\Model\Profile|ProfileInterface getLastItem()
 * @method \Amasty\ShippingBar\Model\Profile|ProfileInterface getNewEmptyItem()
 * @method \Amasty\ShippingBar\Model\Profile[]|ProfileInterface[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(\Amasty\ShippingBar\Model\Profile::class, \Amasty\ShippingBar\Model\ResourceModel\Profile::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function addIsActiveFilter($active = true)
    {
        $this->addFieldToFilter(ProfileInterface::STATUS, $active);

        return $this;
    }

    /**
     * @param int $storeViewId
     * @return $this
     */
    public function addStoreFilter($storeViewId)
    {
        $this->addSetFilter(ProfileInterface::STORES, (int)$storeViewId);

        return $this;
    }

    /**
     * @param int $groupId
     * @return $this
     */
    public function addCustomerGroupFilter($groupId)
    {
        $this->addSetFilter(ProfileInterface::CUSTOMER_GROUPS, (int)$groupId, true);

        return $this;
    }

    /**
     * @param string $page
     * @return $this
     */
    public function addPagesFilter($page)
    {
        $this->addSetFilter(ProfileInterface::PAGES, $page);

        return $this;
    }

    /**
     * @param string $field
     * @param int|string $value
     * @param bool $allowZero
     *
     * @return $this
     */
    protected function addSetFilter($field, $value, $allowZero = false)
    {
        $condition = [['null' => true]];
        if (!empty($value) || ($allowZero && $value === 0)) {
            $condition[] = ['finset' => $value];
        }

        $this->addFieldToFilter(
            array_fill(0, count($condition), $field),
            $condition
        );

        return $this;
    }
}
