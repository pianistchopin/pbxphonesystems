<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;

class Session extends \Magento\Checkout\Model\Session
{
    /**
     * @var \Amasty\RequestQuote\Model\QuoteRepository
     */
    private $amastyQuoteRepository;

    /**
     * @var \Amasty\RequestQuote\Model\QuoteFactory
     */
    private $amastyQuoteFactory;

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
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Amasty\RequestQuote\Model\QuoteRepository $amastyQuoteRepository,
        \Amasty\RequestQuote\Model\QuoteFactory $amastyQuoteFactory
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $orderFactory,
            $customerSession,
            $quoteRepository,
            $remoteAddress,
            $eventManager,
            $storeManager,
            $customerRepository,
            $quoteIdMaskFactory,
            $quoteFactory
        );
        $this->amastyQuoteRepository = $amastyQuoteRepository;
        $this->amastyQuoteFactory = $amastyQuoteFactory;
    }

    /**
     * @return Quote
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getQuote()
    {
        $this->_eventManager->dispatch('custom_quote_process', ['checkout_session' => $this]);

        if ($this->_quote === null) {
            $quote = $this->amastyQuoteFactory->create();
            if ($this->getQuoteId()) {
                try {
                    $quote = $this->amastyQuoteRepository->getActive($this->getQuoteId());

                    if ($quote->getQuoteCurrencyCode() != $this->_storeManager->getStore()->getCurrentCurrencyCode()) {
                        $quote->setQuoteCurrencyCode($this->_storeManager->getStore()->getCurrentCurrencyCode());
                        $quote->setStore($this->_storeManager->getStore());
                        $this->amastyQuoteRepository->save($quote->collectTotals());
                        $quote = $this->amastyQuoteRepository->get($this->getQuoteId());
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->setQuoteId(null);
                }
            }

            if (!$this->getQuoteId()) {
                if ($this->_customerSession->isLoggedIn() || $this->_customer) {
                    $customerId = $this->_customer
                        ? $this->_customer->getId()
                        : $this->_customerSession->getCustomerId();
                    try {
                        $quote = $this->amastyQuoteRepository->getActiveForCustomer($customerId);
                        $quote->setQuoteCurrencyCode($this->_storeManager->getStore()->getCurrentCurrencyCode());
                        $this->setQuoteId($quote->getId());
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    }
                } else {
                    $quote->setIsCheckoutCart(true);
                    $this->_eventManager->dispatch('checkout_quote_init', ['quote' => $quote]);
                }
            }

            if ($this->_customer) {
                $quote->setCustomer($this->_customer);
            } elseif ($this->_customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->_customerSession->getCustomerId()));
            }

            $quote->setStore($this->_storeManager->getStore());
            $this->_quote = $quote;
        }

        if (!$this->isQuoteMasked() && !$this->_customerSession->isLoggedIn() && $this->getQuoteId()) {
            $quoteId = $this->getQuoteId();
            /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
            if ($quoteIdMask->getMaskedId() === null) {
                $quoteIdMask->setQuoteId($quoteId)->save();
            }
            $this->setIsQuoteMasked(true);
        }

        $remoteAddress = $this->_remoteAddress->getRemoteAddress();
        if ($remoteAddress) {
            $this->_quote->setRemoteIp($remoteAddress);
            $xForwardIp = $this->request->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quote->setXForwardedFor($xForwardIp);
        }

        return $this->_quote;
    }

    /**
     * @return string
     */
    protected function _getQuoteIdKey()
    {
        return 'amasty_quote_id_' . $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return $this
     */
    public function loadCustomerQuote()
    {
        if (!$this->_customerSession->getCustomerId()) {
            return $this;
        }

        $this->_eventManager->dispatch('load_customer_quote_before', ['checkout_session' => $this]);

        try {
            $customerQuote = $this->amastyQuoteRepository->getForCustomer($this->_customerSession->getCustomerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerQuote = $this->amastyQuoteFactory->create();
        }
        $customerQuote->setStoreId($this->_storeManager->getStore()->getId());

        if ($customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
            if ($this->getQuoteId()) {
                $this->amastyQuoteRepository->save(
                    $customerQuote->merge($this->getQuote())->collectTotals()
                );
            }

            $this->setQuoteId($customerQuote->getId());

            if ($this->_quote) {
                $this->amastyQuoteRepository->delete($this->_quote);
            }
            $this->_quote = $customerQuote;
        } else {
            $this->getQuote()->getBillingAddress();
            $this->getQuote()->getShippingAddress();
            $this->getQuote()->setCustomer($this->_customerSession->getCustomerDataObject())
                ->setTotalsCollectedFlag(false)
                ->collectTotals();
            $this->amastyQuoteRepository->save($this->getQuote());
        }
        return $this;
    }

    /**
     * @return bool True if quote restored successfully, false otherwise
     */
    public function restoreQuote()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getLastRealOrder();
        if ($order->getId()) {
            try {
                $quote = $this->amastyQuoteRepository->get($order->getQuoteId());
                $quote->setReservedOrderId(null);
                $this->amastyQuoteRepository->save($quote);
                $this->replaceQuote($quote)->unsLastRealOrderId();
                $this->_eventManager->dispatch('restore_quote', ['order' => $order, 'quote' => $quote]);
                return true;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            }
        }
        return false;
    }

    /**
     * @param string $sectionName
     */
    public function flushSection($sectionName)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->getCookiePath());
        $sectionDataIds = json_decode($this->cookieManager->getCookie('section_data_ids', '{}'), true);
        $sectionDataIds[$sectionName] = isset($sectionDataIds[$sectionName]) ?
            $sectionDataIds[$sectionName] + 1000 :
            1000;
        $this->cookieManager->deleteCookie('section_data_ids');
        $this->cookieManager->setPublicCookie(
            'section_data_ids',
            json_encode($sectionDataIds),
            $metadata
        );
    }

    /**
     * @return bool|\Magento\Quote\Api\Data\CartInterface
     */
    public function getLastQuote()
    {
        $quote = false;
        $quoteId = $this->getLastQuoteId();
        if ($quoteId) {
            $quote = $this->amastyQuoteRepository->get($quoteId);
        }

        return $quote;
    }
}
