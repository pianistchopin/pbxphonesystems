<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\ResourceModel\Quote\Account;

class CollectionFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Amasty\RequestQuote\Model\ResourceModel\Quote\Collection::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * @param int|null $customerId
     * @return \Amasty\RequestQuote\Model\ResourceModel\Quote\Collection
     */
    public function create($customerId = null)
    {
        $collection = $this->_objectManager->create($this->_instanceName);
        if ($customerId) {
            $collection->addFieldToFilter('main_table.customer_id', $customerId);
        }

        return $collection;
    }
}
