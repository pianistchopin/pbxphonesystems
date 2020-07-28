<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model;

use Amasty\RequestQuote\Api\Data\QuoteInterface;
use Amasty\RequestQuote\Model\Email\AdminNotification;
use Amasty\RequestQuote\Helper\Data;
use Amasty\RequestQuote\Model\Source\Status;
use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class Quote extends \Magento\Quote\Model\Quote implements QuoteInterface
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
     * @var array
     */
    private $ignoreProductTypes = [
        'giftcard'
    ];

    /**
     * @var null|\Amasty\RequestQuote\Model\Source\Status
     */
    private $statusSource = null;

    /**
     * @var null|\Magento\Directory\Model\CurrencyFactory
     */
    private $currencyDirectoryFactory = null;

    /**
     * @var null|Currency
     */
    private $quoteCurrency = null;

    /**
     * @var null|Currency
     */
    private $baseCurrency = null;

    /**
     * @var null|ResolverInterface
     */
    private $localeResolver = null;

    /**
     * @var null|TimezoneInterface
     */
    private $timezone = null;

    /**
     * @var null|MessageManager
     */
    private $messageManager = null;

    /**
     * @var null|Data
     */
    private $helper = null;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\RequestQuote\Model\ResourceModel\Quote::class);
        $this->statusSource = $this->_data['status_source'];
        $this->currencyDirectoryFactory = $this->_data['currency_factory'];
        $this->localeResolver = $this->_data['locale_resolver'];
        $this->timezone = $this->_data['timezone'];
        $this->messageManager = $this->_data['messageManager'];
        $this->helper = $this->_data['helper'];
    }

    public function getStatus()
    {
        if (!$this->hasData('status')) {
            $this->setData('status', Status::CREATED);
            $this->setData(QuoteInterface::ADMIN_NOTIFICATION_SEND, AdminNotification::NOT_SENT);
        }
        return $this->getData('status');
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getStatus() == Status::CREATED;
    }

    /**
     * @param int $itemId
     * @param \Magento\Framework\DataObject $buyRequest
     * @param null $params
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $resultItem =  parent::updateItem($itemId, $buyRequest, $params);
        if ($buyRequest->getData('price')) {
            $resultItem->setPrice($buyRequest->getData('price'));
        }
        return $resultItem;
    }

    /**
     * @return bool
     */
    public function canOrder()
    {
        return $this->getStatus() == Status::APPROVED;
    }

    /**
     * @return bool
     */
    public function canHold()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canRenew()
    {
        return in_array($this->getStatus(), [Status::COMPLETE, Status::CANCELED]);
    }

    /**
     * @return bool
     */
    public function canApprove()
    {
        return in_array($this->getStatus(), [
            Status::PENDING,
            Status::ADMIN_CREATED
        ]);
    }

    /**
     * @return bool
     */
    public function canClose()
    {
        return !$this->canRenew() && $this->getStatus() != Status::EXPIRED;
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        return in_array($this->getStatus(), [
            Status::PENDING,
            Status::APPROVED,
            Status::ADMIN_CREATED
        ]) && !$this->getData('is_active');
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData('status', $status);
        return $this;
    }

    /**
     * @return string
     */
    public function prepareIncrementId()
    {
        return sprintf("%s%'.09d", $this->getStoreId(), $this->getId());
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->statusSource->getStatusLabel($this->getStatus());
    }

    /**
     * @return Currency
     */
    public function getQuoteCurrency()
    {
        if ($this->quoteCurrency === null) {
            $this->quoteCurrency = $this->currencyDirectoryFactory->create();
            $this->quoteCurrency->load($this->getQuoteCurrencyCode());
        }

        return $this->quoteCurrency;
    }

    /**
     * @return Currency
     */
    public function getBaseCurrency()
    {
        if ($this->baseCurrency === null) {
            $this->baseCurrency = $this->currencyDirectoryFactory->create();
            $this->baseCurrency->load($this->getBaseCurrencyCode());
        }

        return $this->baseCurrency;
    }

    /**
     * @param   float $price
     * @param   bool  $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param $price
     * @param bool $addBrackets
     * @return string
     */
    public function formatBasePrice($price, $addBrackets = false)
    {
        return $this->formatBasePricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getQuoteCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatBasePricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * @inheritdoc
     */
    public function collectTotals()
    {
        if ($this->getTotalsCollectedFlag()) {
            return $this;
        }

        return parent::collectTotals();
    }

    /**
     * @return string
     */
    public function prepareCustomerName()
    {
        return ($this->getCustomerPrefix() ? $this->getCustomerPrefix() . ' ' : '')
            . $this->getCustomerFirstname()
            . ($this->getCustomerMiddlename() ? ' ' . $this->getCustomerMiddlename() : '')
            . ' ' . $this->getCustomerLastname()
            . ($this->getCustomerSuffix() ? ' ' . $this->getCustomerSuffix() : '');
    }

    /**
     * @param int $quoteId
     * @return $this
     */
    public function loadMagentoQuoteByIdWithoutStore($quoteId)
    {
        $this->_getResource()->loadMagentoQuoteByIdWithoutStore($this, $quoteId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatted($format)
    {
        return $this->timezone->formatDateTime(
            new \DateTime($this->getCreatedAt()),
            $format,
            $format,
            $this->localeResolver->getDefaultLocale(),
            $this->timezone->getConfigTimezone('store', $this->getStore())
        );
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        return $this->getQuoteCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * @return string
     */
    public function getQuoteCurrencyCode()
    {
        return $this->getData('quote_currency_code');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param bool $advancedMode
     * @param bool $inQuote
     *
     * @return bool
     */
    public function advancedMerge(\Magento\Quote\Model\Quote $quote, $advancedMode, $inQuote)
    {
        $result = false;
        $itemsForRemove = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($inQuote) {
                if (in_array($item->getProductType(), $this->ignoreProductTypes)) {
                    $this->messageManager->addNoticeMessage(
                        __('The Gift Card can not be converted from shopping cart to quote.')
                    );
                    continue;
                }
                $product = $this->productRepository->getById($item->getProductId(), true, $this->getStoreId());
                if ($product->getData(Data::ATTRIBUTE_NAME_HIDE_BUY_BUTTON) ||
                    !empty(array_uintersect(
                        $product->getCategoryIds(),
                        $this->helper->getExcludeCategories(),
                        'strcmp'
                    ))
                ) {
                    $this->messageManager->addNoticeMessage(
                        __('One or several Products can not be converted from shopping cart to quote.')
                    );
                    continue;
                }
            }
            $result = true;
            $found = false;
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $this->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $this->addItem($newChild);
                    }
                }
            }
            $itemsForRemove[] = $item->getItemId();
        }

        if ($result) {
            $this->setStoreId($quote->getStoreId());

            if ($advancedMode && count($this->getAllAddresses()) == 0) {
                foreach ($quote->getAllAddresses() as $address) {
                    $newAddress = clone $address;
                    $this->addAddress($newAddress);
                }

                foreach ($this->customerFields as $field) {
                    $value = $quote->getData($field);
                    $this->setData($field, $value);
                }
            }
            foreach ($itemsForRemove as $itemId) {
                $quote->removeItem($itemId);
            }
            $this->setTotalsCollectedFlag(false);
            $this->collectTotals();
        }

        return $result;
    }

    /**
     * @param   \Magento\Quote\Model\Quote\Item $item
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItem(\Magento\Quote\Model\Quote\Item $item)
    {
        $item->setNoDiscount(true);
        return parent::addItem($item);
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->setForcedCurrency($this->getQuoteCurrency());
        return parent::beforeSave();
    }
}
