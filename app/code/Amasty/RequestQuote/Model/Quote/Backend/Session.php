<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote\Backend;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Session
 */
class Session extends \Magento\Framework\Session\SessionManager
{
    /**
     * @var array
     */
    private $customerFields = [
        'customer_id',
        'customer_tax_class_id',
        'customer_group_id',
        'customer_email',
        'customer_prefix',
        'customer_firstname',
        'customer_middlename',
        'customer_lastname',
        'customer_suffix',
        'customer_dob',
        'customer_note',
        'customer_note_notify',
        'customer_is_guest'
    ];

    /**
     * @var \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    private $quote;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Amasty\RequestQuote\Api\QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var \Amasty\RequestQuote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Amasty\RequestQuote\Model\Quote
     */
    private $parentQuote;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        CustomerRepositoryInterface $customerRepository,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        GroupManagementInterface $groupManagement,
        \Amasty\RequestQuote\Model\QuoteFactory $quoteFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->groupManagement = $groupManagement;
        $this->quoteFactory = $quoteFactory;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        if ($this->storeManager->hasSingleStore()) {
            $this->setStoreId($this->storeManager->getStore(true)->getId());
        }
    }

    /**
     * @return $this
     */
    public function reloadQuote()
    {
        $this->quote = $this->quoteRepository->getMagentoQuote($this->getQuoteId(), [$this->getStoreId()]);
        return $this;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote($editMode = false)
    {
        if ($this->quote === null) {
            $this->quote = $this->quoteFactory->create();
            $this->quote->setStatus(\Amasty\RequestQuote\Model\Source\Status::ADMIN_NEW);

            if (!$this->getQuoteId()) {
                $this->quote->setCustomerGroupId($this->groupManagement->getDefaultGroup()->getId());
                $this->quote->setIsActive(false);
                $this->quote->setStoreId($this->getStoreId());

                $this->quoteRepository->save($this->quote);
                $this->setQuoteId($this->quote->getId());
                if ($editMode) {
                    $this->quote = $this->quoteRepository->getMagentoQuote($this->getQuoteId(), [$this->getStoreId()]);
                    $this->quote->setForcedCurrency($this->quote->getQuoteCurrency());
                } else {
                    $this->quote = $this->quoteRepository->get($this->getQuoteId(), [$this->getStoreId()]);
                }
            } else {
                if ($editMode) {
                    $this->quote = $this->quoteRepository->getMagentoQuote($this->getQuoteId(), ['*']);
                } else {
                    $this->quote = $this->quoteRepository->get($this->getQuoteId(), ['*']);
                }
                $this->quote->setStoreId($this->getStoreId());
            }

            if ($this->getCustomerId() && $this->getCustomerId() != $this->quote->getCustomerId()) {
                $customer = $this->customerRepository->getById($this->getCustomerId());
                $this->quote->assignCustomer($customer);
                $this->quoteRepository->save($this->quote);
            }

            $this->quote->setIgnoreOldQty(true);
            $this->quote->setIsSuperMode(true);
        }

        return $this->quote;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote|\Magento\Quote\Model\Quote|mixed
     */
    public function getParentQuote()
    {
        if ($this->parentQuote === null) {
            $this->parentQuote = $this->quoteFactory->create();

            if ($this->getParentId($this->quote->getId())) {
                $this->parentQuote = $this->quoteRepository->get(
                    $this->getParentId($this->quote->getId()),
                    [$this->getStoreId()]
                );
                $this->parentQuote->setStoreId($this->getStoreId());
            }

            $this->parentQuote->setIgnoreOldQty(true);
            $this->parentQuote->setIsSuperMode(true);
        }

        return $this->parentQuote;
    }

    /**
     * @param $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        $this->setQuoteId($this->quote->getId());
        $this->setStoreId($this->quote->getStoreId());
        $this->setCustomerId($this->quote->getCustomerId());
        return $this;
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore($this->getStoreId());
            $currencyId = $this->getCurrencyId();
            if ($currencyId) {
                $this->store->setCurrentCurrencyCode($currencyId);
            }
        }
        return $this->store;
    }

    /**
     * @param $quoteId
     * @param $parentId
     * @return $this
     */
    public function setQuoteIdParrentId($quoteId, $parentId)
    {
        $quoteIds = $this->getData('parent_id_by_quote_id');
        $quoteIds[$quoteId] = $parentId;
        $this->setData('parent_id_by_quote_id', $quoteIds);
        return $this;
    }

    /**
     * @param $quoteId = null
     * @return null
     */
    public function getParentId($quoteId = null)
    {
        if (!$quoteId) {
            $quoteId = $this->getQuoteId();
        }

        $quoteIds = $this->getData('parent_id_by_quote_id');
        return isset($quoteIds[$quoteId]) ? $quoteIds[$quoteId] : null;
    }

    /**
     * @param $quoteId
     * @return null
     */
    public function getChildId($quoteId)
    {
        $quoteIds = array_flip((array)$this->getData('parent_id_by_quote_id'));
        return isset($quoteIds[$quoteId]) ? $quoteIds[$quoteId] : null;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            $customer = $this->customerRepository->getById($this->getCustomerId());
        } catch (NoSuchEntityException $exception) {
            $customer = null;
        }

        return $customer;
    }

    /**
     * @param |Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function initFromQuote($quote)
    {
        $newQuote = $this->quoteFactory->create();
        $newQuote->setStatus(Status::ADMIN_NEW);

        foreach ($quote->getAllVisibleItems() as $item) {
            $newItem = clone $item;
            $newQuote->addItem($newItem);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $newChild = clone $child;
                    $newChild->setParentItem($newItem);
                    $newQuote->addItem($newChild);
                }
            }
        }
        foreach ($quote->getAllAddresses() as $address) {
            $newAddress = clone $address;
            $newQuote->addAddress($newAddress);
        }
        $newQuote->setStoreId($quote->getStoreId())
            ->setRemarks($quote->getRemarks());
        foreach ($this->customerFields as $field) {
            $value = $quote->getData($field);
            $newQuote->setData($field, $value);
        }
        $newQuote->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
        $newQuote->setForcedCurrency($quote->getQuoteCurrency());
        $newQuote->collectTotals();
        $newQuote->setStoreId($quote->getStoreId());
        $newQuote->setIsActive(false);
        $this->quoteRepository->save($newQuote);
        $this->setQuoteId($newQuote->getId());

        return $this;
    }
}
