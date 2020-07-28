<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Model\ResourceModel\Label;

use Amasty\ShippingBar\Api\Data\LabelInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct() //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_init(\Amasty\ShippingBar\Model\Label::class, \Amasty\ShippingBar\Model\ResourceModel\Label::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param int $profileId
     *
     * @return $this
     */
    public function addProfileFilter($profileId)
    {
        $this->addFieldToFilter(LabelInterface::PROFILE_ID, $profileId);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->addFieldToFilter(LabelInterface::STORE_ID, $storeId);

        return $this;
    }
}
