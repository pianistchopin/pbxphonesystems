<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Manager
 */
class Manager
{
    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $email
     * @param string$firstName
     * @param string $lastName
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function register($email, $firstName, $lastName)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->create();
        $store = $this->storeManager->getStore();

        return $customer->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail($email)
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setCreatedIn($store->getName());
    }
}
