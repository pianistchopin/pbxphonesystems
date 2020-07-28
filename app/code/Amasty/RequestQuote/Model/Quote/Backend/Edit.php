<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote\Backend;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;

class Edit extends \Magento\Framework\DataObject implements \Magento\Checkout\Model\Cart\CartInterface
{
    /**
     * Xml default email domain path
     */
    const XML_PATH_DEFAULT_EMAIL_DOMAIN = 'customer/create_account/email_domain';

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $session;

    /**
     * @var \Amasty\RequestQuote\Model\Quote
     */
    private $quote;

    /**
     * @var boolean
     */
    private $needCollect;

    /**
     * @var boolean
     */
    private $needCollectCart = false;

    /**
     * @var boolean
     */
    private $isValidate = false;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $salesConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $metadataFormFactory;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Updater
     */
    protected $quoteItemUpdater;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $customerMapper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var QuoteRepository
     */
    private $magentoQuoteRepository;

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
     * @var \Magento\Sales\Model\AdminOrder\EmailSender
     */
    private $emailSender;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockState;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Config $salesConfig,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Message\ManagerInterface $messageManager,     
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,          
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Amasty\RequestQuote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\QuoteRepository $magentoQuoteRepository,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Amasty\RequestQuote\Helper\Data $helper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->salesConfig = $salesConfig;
        $this->session = $quoteSession;
        $this->logger = $logger;
        $this->objectCopyService = $objectCopyService;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
        $this->metadataFormFactory = $metadataFormFactory;
        $this->customerFactory = $customerFactory;
        $this->groupRepository = $groupRepository;
        $this->scopeConfig = $scopeConfig;
        $this->emailSender = $emailSender;
        $this->stockRegistry = $stockRegistry;
        $this->quoteItemUpdater = $quoteItemUpdater;
        $this->objectFactory = $objectFactory;
        $this->quoteRepository = $quoteRepository;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->accountManagement = $accountManagement;
        $this->customerMapper = $customerMapper;
        $this->quoteManagement = $quoteManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->orderManagement = $orderManagement;
        $this->quoteFactory = $quoteFactory;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        parent::__construct($data);
        $this->stockState = $stockState;
    }

    /**
     * @param boolean $flag
     * @return $this
     */
    public function setIsValidate($flag)
    {
        $this->isValidate = (bool)$flag;
        return $this;
    }

    /**
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidate()
    {
        return $this->isValidate;
    }

    /**
     * @param int|\Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Model\Quote\Item|false
     */
    protected function getQuoteItem($item)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            return $item;
        } elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }

        return false;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setRecollect($flag)
    {
        $this->needCollect = $flag;
        return $this;
    }

    /**
     * @return $this
     */
    public function recollectQuote()
    {
        if ($this->needCollectCart === true) {
            $this->getQuote()->collectTotals();
            $this->quoteRepository->save($this->getQuote());
        }
        $this->setRecollect(true);

        return $this;
    }

    /**
     * @return $this
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->needCollect) {
            $this->getQuote()->collectTotals();
        }

        $this->magentoQuoteRepository->save($this->getQuote());
        return $this;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote|\Magento\Quote\Model\Quote|mixed
     */
    public function saveFromQuote()
    {
        $quote = $this->getQuote();
        $parentQuote = $this->getSession()->getParentQuote();
        if (!$parentQuote->getId()) {
            $quote->collectTotals();
            $this->updateQuoteData($quote);
            $this->quoteRepository->save($quote);
            return $quote;
        }

        $parentQuote->removeAllItems();

        foreach ($quote->getAllVisibleItems() as $item) {
            $newItem = clone $item;
            $parentQuote->addItem($newItem);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $newChild = clone $child;
                    $newChild->setParentItem($newItem);
                    $parentQuote->addItem($newChild);
                }
            }
        }
        $this->updateQuoteData($parentQuote);
        $parentQuote->collectTotals();
        $this->quoteRepository->save($parentQuote);
        $this->getSession()->setQuote($parentQuote);

        if ($parentQuote->getStatus() != Status::ADMIN_NEW) {
            $this->emailSender->sendQuoteEditEmail($parentQuote);
        }

        return $parentQuote;
    }

    /**
     * @param $quote
     * @return mixed
     */
    private function updateQuoteData($quote)
    {
        if ($this->getData('note')) {
            $remarks = [];
            if ($quote->getRemarks()) {
                $remarks = $this->jsonDecoder->decode($quote->getRemarks());
            }
            $remarks['admin_note'] = $this->getData('note');
            $quote->setRemarks($this->jsonEncoder->encode($remarks));
        }
        if ($this->helper->getExpirationTime() !== null &&
            $this->getData('expired_date') &&
            $this->dateTime->gmtDate('y-m-d', $this->getData('expired_date')) &&
            $this->isDateChanged($quote->getData('expired_date'), $this->getData('expired_date'))
        ) {
            $quote->setExpiredDate($this->dateTime->gmtDate(null, $this->getData('expired_date')));
        }
        if ($this->helper->getReminderTime() !== null &&
            $this->getData('reminder_date') &&
            $this->dateTime->gmtDate('y-m-d', $this->getData('reminder_date')) &&
            $this->isDateChanged($quote->getData('reminder_date'), $this->getData('reminder_date'))
        ) {
            $quote->setReminderDate($this->dateTime->gmtDate(null, $this->getData('reminder_date')));
        }


        return $this;
    }

    /**
     * @param $currentDate
     * @param $newDate
     *
     * @return bool
     */
    private function isDateChanged($currentDate, $newDate)
    {
        return !$currentDate ||
            $this->dateTime->gmtDate('y-m-d', $newDate) != $this->dateTime->gmtDate('y-m-d', $currentDate);
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->getSession()->getQuote(true);
        }

        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }

        return $groupId;
    }

    /**
     * @param int|\Magento\Quote\Model\Quote\Item $item
     * @param string $moveTo
     * @param int $qty
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        $item = $this->getQuoteItem($item);
        if ($item) {
            $removeItem = false;
            $moveTo = explode('_', $moveTo);
            switch ($moveTo[0]) {
                case 'order':
                    $info = $item->getBuyRequest();
                    $info->setOptions($this->prepareOptionsForRequest($item))->setQty($qty);

                    $product = $this->objectManager->create(
                        \Magento\Catalog\Model\Product::class
                    )->setStoreId(
                        $this->getQuote()->getStoreId()
                    )->load(
                        $item->getProduct()->getId()
                    );

                    $product->setSkipCheckRequiredOption(true);
                    $newItem = $this->getQuote()->addProduct($product, $info);

                    if (is_string($newItem)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__($newItem));
                    }
                    $product->unsSkipCheckRequiredOption();
                    $newItem->checkData();
                    $this->needCollectCart = true;
                    break;
                case 'cart':
                    $cart = $this->getCustomerCart();
                    if ($cart && is_null($item->getOptionByCode('additional_options'))) {
                        //options and info buy request
                        $product = $this->objectManager->create(
                            \Magento\Catalog\Model\Product::class
                        )->setStoreId(
                            $this->getQuote()->getStoreId()
                        )->load(
                            $item->getProduct()->getId()
                        );

                        $info = $item->getOptionByCode('info_buyRequest');
                        if ($info) {
                            $info = new \Magento\Framework\DataObject(
                                $this->jsonDecoder->decode($info->getValue())
                            );
                            $info->setQty($qty);
                            $info->setOptions($this->prepareOptionsForRequest($item));
                        } else {
                            $info = new \Magento\Framework\DataObject(
                                [
                                    'product_id' => $product->getId(),
                                    'qty' => $qty,
                                    'options' => $this->prepareOptionsForRequest($item)
                                ]
                            );
                        }

                        $cartItem = $cart->addProduct($product, $info);
                        if (is_string($cartItem)) {
                            throw new \Magento\Framework\Exception\LocalizedException(__($cartItem));
                        }
                        $cartItem->setPrice($item->getProduct()->getPrice());
                        $this->needCollectCart = true;
                        $removeItem = true;
                    }
                    break;
                case 'wishlist':
                    $wishlist = null;
                    if (!isset($moveTo[1])) {
                        $wishlist = $this->objectManager->create(
                            \Magento\Wishlist\Model\Wishlist::class
                        )->loadByCustomerId(
                            $this->getSession()->getCustomerId(),
                            true
                        );
                    } else {
                        $wishlist = $this->objectManager->create(
                            \Magento\Wishlist\Model\Wishlist::class)->load($moveTo[1]);
                        if (!$wishlist->getId() || $wishlist->getCustomerId() != $this->getSession()->getCustomerId()
                        ) {
                            $wishlist = null;
                        }
                    }
                    if (!$wishlist) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('We can\'t find this wish list.')
                        );
                    }
                    $wishlist->setStore(
                        $this->getSession()->getStore()
                    )->setSharedStoreIds(
                        $this->getSession()->getStore()->getWebsite()->getStoreIds()
                    );

                    if ($wishlist->getId() && $item->getProduct()->isVisibleInSiteVisibility()) {
                        $info = $item->getBuyRequest();
                        $info->setOptions(
                            $this->prepareOptionsForRequest($item)
                        )->setQty(
                            $qty
                        )->setStoreId(
                            $this->getSession()->getStoreId()
                        );
                        $wishlist->addNewItem($item->getProduct(), $info);
                        $removeItem = true;
                    }
                    break;
                case 'remove':
                    $removeItem = true;
                    break;
                default:
                    break;
            }
            if ($removeItem) {
                $this->getQuote()->deleteItem($item);
            }
            $this->setRecollect(true);
        }

        return $this;
    }

    /**
     * @param int $itemId
     * @return $this
     */
    public function removeItem($itemId)
    {
        $this->removeQuoteItem($itemId);
        return $this;
    }

    /**
     * @param int $item
     * @return $this
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * @param int|\Magento\Catalog\Model\Product $product
     * @param array|float|int|\Magento\Framework\DataObject $config
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProduct($product, $config = 1)
    {
        if (!is_array($config) && !$config instanceof \Magento\Framework\DataObject) {
            $config = ['qty' => $config];
        }
        $config = new \Magento\Framework\DataObject($config);

        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product;

            try {
                $product = $this->productRepository->getById(
                    $productId,
                    false,
                    $this->getSession()->getStoreId()
                );
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }

        if (!in_array(
            $product->getTypeId(),
            [
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
                \Magento\Bundle\Model\Product\Type::TYPE_CODE
            ]
        )) {
            try {
                $this->stockState->checkQuoteItemQty(
                    $product->getId(),
                    $config->getQty(),
                    $config->getQty(),
                    $config->getQty(),
                    $product->getStore()->getWebsiteId()
                );
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }

        $quote = $this->getQuote();

        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $quote->getStore()->getWebsiteId());
        if ($stockItem->getIsQtyDecimal()) {
            $product->setIsQtyDecimal(1);
        } else {
            $config->setQty((int)$config->getQty());
        }

        $product->addCustomOption('amasty_quote_price', null);

        $item = $quote->addProduct(
            $product,
            $config,
            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
        );

        $item->setNoDiscount(true);

        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }
        $item->checkData();
        $this->setRecollect(true);

        return $this;
    }

    /**
     * @param array $products
     * @return $this
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (double)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * @param array $items
     * @return $this
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    public function updateQuoteItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }

        try {
            foreach ($items as $itemId => $info) {
                if (!empty($info['configured'])) {
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $info['qty'] = (double)$item->getQty();
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                    $info['price'] = (double)$info['price'];
                }
                if (!$item->getOptionByCode('amasty_quote_price')) {
                    $priceOption = $this->objectFactory->create(
                        []
                    )->setCode(
                        'amasty_quote_price'
                    )->setValue(
                        $item->getPrice()
                    )->setProduct(
                        $item->getProduct()
                    );
                    $item->addOption($priceOption);
                }
                $this->quoteItemUpdater->update($item, $info);
                if ($item && !empty($info['action'])) {
                    $this->moveQuoteItem($item, $info['action'], $item->getQty());
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->recollectQuote();
            throw $e;
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        $this->recollectQuote();

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    protected function prepareOptionsForRequest($item)
    {
        $newInfoOptions = [];
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_' . $optionId)->getValue();

                $group = $this->objectManager->get(
                    \Magento\Catalog\Model\Product\Option::class
                )->groupFactory(
                    $option->getType()
                )->setOption(
                    $option
                )->setQuoteItem(
                    $item
                );

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }

        return $newInfoOptions;
    }

    /**
     * @param float|int $price
     * @return float|int
     */
    protected function parsePrice($price)
    {
        $price = $this->objectManager->get(\Magento\Framework\Locale\FormatInterface::class)->getNumber($price);
        $price = $price > 0 ? $price : 0;

        return $price;
    }

    /**
     * @return void
     */
    public function collectRates()
    {
        $this->getQuote()->collectTotals();
        return $this;
    }

    /**
     * @param   array $data
     * @return  $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        }
        return $this;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    protected function customerIsInStore($store)
    {
        $customer = $this->getQuote()->getCustomer();

        return $customer->getWebsiteId() == $store->getWebsiteId()
            || $this->accountManagement->isCustomerInStore($customer->getWebsiteId(), $store->getId());
    }

    /**
     * @param $quote
     * @return \Magento\Quote\Model\Quote
     */
    public function initFromQuote($quote)
    {
        $newQuote = $this->quoteFactory->create();
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
        $newQuote->setIsActive(false);
        $this->magentoQuoteRepository->save($newQuote);
        $this->session->setQuoteId($newQuote->getId());
        $this->session->setQuoteIdParrentId($newQuote->getId(), $quote->getId());
        return $newQuote;
    }
}
