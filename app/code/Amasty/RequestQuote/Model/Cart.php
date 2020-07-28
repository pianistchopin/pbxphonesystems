<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Cart extends \Magento\Checkout\Model\Cart
{
    /**
     * @var Quote\Session
     */
    private $amastyQuoteSession;

    /**
     * @var QuoteRepository
     */
    private $amastyQuoteRepository;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\ResourceModel\Cart $resourceCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        ProductRepositoryInterface $productRepository,
        \Amasty\RequestQuote\Model\Quote\Session $amastyQuoteSession,
        \Amasty\RequestQuote\Model\QuoteRepository $amastyQuoteRepository,
        array $data = []
    ) {
        $this->amastyQuoteSession = $amastyQuoteSession;
        $this->amastyQuoteRepository = $amastyQuoteRepository;
        parent::__construct(
            $eventManager,
            $scopeConfig,
            $storeManager,
            $resourceCart,
            $checkoutSession,
            $customerSession,
            $messageManager,
            $stockRegistry,
            $stockState,
            $quoteRepository,
            $productRepository,
            $data
        );

    }

    /**
     * @return Quote\Session
     * @codeCoverageIgnore
     */
    public function getQuoteSession()
    {
        return $this->amastyQuoteSession;
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        if (!$this->hasData('quote')) {
            $this->setData('quote', $this->amastyQuoteSession->getQuote());
        }
        return $this->_getData('quote');
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->_eventManager->dispatch('checkout_cart_save_before', ['cart' => $this]);

        $this->getQuote()->getBillingAddress();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->collectTotals();
        $this->amastyQuoteRepository->save($this->getQuote());
        $this->getQuoteSession()->setQuoteId($this->getQuote()->getId());
        $this->_eventManager->dispatch('checkout_cart_save_after', ['cart' => $this]);
        $this->getQuote()->removeAllAddresses();
        return $this;
    }

    /**
     * @return int|float
     */
    public function getSummaryQty()
    {
        $quoteId = $this->getQuoteSession()->getQuoteId();
        if (!$quoteId && $this->getCustomerSession()->isLoggedIn()) {
            $this->getQuoteSession()->getQuote();
            $quoteId = $this->getQuoteSession()->getQuoteId();
        }

        if ($quoteId && $this->_summaryQty === null) {
            $useQty = $this->_scopeConfig->getValue(
                'checkout/cart_link/use_qty',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $this->_summaryQty = $useQty ? $this->getItemsQty() : $this->getItemsCount();
        }
        return $this->_summaryQty;
    }

    /**
     * @param int $itemId
     * @param int|array|\Magento\Framework\DataObject $requestInfo
     * @param null|array|\Magento\Framework\DataObject $updatingParams
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateItem($itemId, $requestInfo = null, $updatingParams = null)
    {
        try {
            $result = parent::updateItem($itemId, $requestInfo, $updatingParams);
        } catch (LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice()) {
                $this->getQuoteSession()->setUseNotice(true);
            }
            $this->getQuoteSession()->setRedirectUrl(
                $this->_checkoutSession->getRedirectUrl()
            );
            throw new LocalizedException($e->getMessage());
        }

        return $result;
    }

    /**
     * @param $productId
     * @return Product
     */
    public function getProductById($productId)
    {
        return $this->_getProduct((int)$productId);
    }
}
