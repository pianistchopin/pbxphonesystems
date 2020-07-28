<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Items;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\Quote\Item;

class Grid extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\AbstractEdit
{
    /**
     * @var bool
     */
    private $moveToCustomerStorage = true;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxData;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        \Amasty\RequestQuote\Model\Quote\Backend\Edit $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        \Amasty\Base\Model\Serializer $serializer,
        array $data = []
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->taxConfig = $taxConfig;
        $this->taxData = $taxData;
        $this->stockRegistry = $stockRegistry;
        $this->stockState = $stockState;
        $this->serializer = $serializer;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_quote_edit_search_grid');
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            $item->setQty($item->getQty());
            if (!$item->getMessage()) {
                $stockItemToCheck = [];
                $childItems = $item->getChildren();
                if (count($childItems)) {
                    foreach ($childItems as $childItem) {
                        $stockItemToCheck[] = $childItem->getProduct()->getId();
                    }
                } else {
                    $stockItemToCheck[] = $item->getProduct()->getId();
                }

                foreach ($stockItemToCheck as $productId) {
                    $check = $this->stockState->checkQuoteItemQty(
                        $productId,
                        $item->getQty(),
                        $item->getQty(),
                        $item->getQty(),
                        $this->getQuote()->getStore()->getWebsiteId()
                    );
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }

            if ($item->getProduct()->getStatus() == ProductStatus::STATUS_DISABLED) {
                $item->setMessage(__('This product is disabled.'));
                $item->setHasError(true);
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }

    /**
     * @return SessionManagerInterface
     */
    public function getSession()
    {
        return $this->getParentBlock()->getSession();
    }

    /**
     * @param Item $item
     * @return float
     */
    public function getItemEditablePrice($item)
    {
        return $item->getCalculationPrice() * 1;
    }

    /**
     * @param Item $item
     * @return float
     */
    public function getOriginalEditablePrice($item)
    {
        if ($item->hasOriginalCustomPrice()) {
            $result = $item->getOriginalCustomPrice() * 1;
        } elseif ($item->hasCustomPrice()) {
            $result = $item->getCustomPrice() * 1;
        } else {
            if ($this->taxData->priceIncludesTax($this->getStore())) {
                $result = $item->getPriceInclTax() * 1;
            } else {
                $result = $item->getOriginalPrice() * 1;
            }
        }
        return $result;
    }

    /**
     * @param Item $item
     * @return float
     */
    public function getItemOrigPrice($item)
    {
        return $this->convertPrice($item->getPrice());
    }

    /**
     * @return bool
     */
    public function displayTotalsIncludeTax()
    {
        $result = $this->taxConfig->displayCartSubtotalInclTax($this->getStore())
            || $this->taxConfig->displayCartSubtotalBoth($this->getStore());
        return $result;
    }

    /**
     * @return false|float
     */
    public function getSubtotal()
    {
        $address = $this->getQuoteAddress();
        if (!$this->displayTotalsIncludeTax()) {
            return $address->getSubtotal();
        }
        if ($address->getSubtotalInclTax()) {
            return $address->getSubtotalInclTax();
        }
        return $address->getSubtotal() + $address->getTaxAmount();
    }

    /**
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        $address = $this->getQuoteAddress();
        if ($this->displayTotalsIncludeTax()) {
            return $address->getSubtotal()
                + $address->getTaxAmount()
                + $address->getDiscountAmount()
                + $address->getDiscountTaxCompensationAmount();
        } else {
            return $address->getSubtotal() + $address->getDiscountAmount();
        }
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getQuote()->getShippingAddress()->getDiscountAmount();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getQuoteAddress()
    {
        if ($this->getQuote()->isVirtual()) {
            return $this->getQuote()->getBillingAddress();
        } else {
            return $this->getQuote()->getShippingAddress();
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function canApplyCustomPrice($item)
    {
        return !$item->isChildrenCalculated();
    }

    /**
     * @param Item $item
     * @return \Magento\Framework\Phrase|string
     */
    public function getQtyTitle($item)
    {
        $prices = $item->getProduct()
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)
            ->getTierPriceList();
        if ($prices) {
            $info = [];
            foreach ($prices as $data) {
                $price = $this->convertPrice($data['price']);
                $info[] = __('Buy %1 for price %2', $data['price_qty'], $price);
            }
            return implode(', ', $info);
        } else {
            return __('Item qty');
        }
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getTierHtml($item)
    {
        $html = '';
        $prices = $item->getProduct()->getTierPrice();
        if ($prices) {
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $info = $this->_getBundleTierPriceInfo($prices);
            } else {
                $info = $this->_getTierPriceInfo($prices);
            }

            $html = implode('<br />', $info);
        }
        return $html;
    }

    /**
     * @param array $prices
     * @return string[]
     */
    protected function _getBundleTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $info[] = __('%1 with %2 discount each', $qty, $data['price'] * 1 . '%');
        }
        return $info;
    }

    /**
     * @param array $prices
     * @return string[]
     */
    protected function _getTierPriceInfo($prices)
    {
        $info = [];
        foreach ($prices as $data) {
            $qty = $data['price_qty'] * 1;
            $price = $this->convertPrice($data['price']);
            $info[] = __('%1 for %2', $qty, $price);
        }
        return $info;
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getCustomOptions(Item $item)
    {
        $optionStr = '';
        $this->moveToCustomerStorage = true;
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                if ($option) {
                    $optionStr .= $option->getTitle() . ':';
                    $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $optionStr .= $group->getEditableOptionValue($quoteItemOption->getValue());
                    $optionStr .= "\n";
                }
            }
        }
        return $optionStr;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getMoveToCustomerStorage()
    {
        return $this->moveToCustomerStorage;
    }

    /**
     * @param Item $item
     * @return string
     */
    public function displaySubtotalInclTax($item)
    {
        if ($item->getTaxBeforeDiscount()) {
            $tax = $item->getTaxBeforeDiscount();
        } else {
            $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        }
        return $this->formatPrice($item->getRowTotal() + $tax);
    }

    /**
     * @param Item $item
     * @return float
     */
    public function displayOriginalPriceInclTax($item)
    {
        $tax = 0;
        if ($item->getTaxPercent()) {
            $tax = $item->getPrice() * ($item->getTaxPercent() / 100);
        }
        return $this->convertPrice($item->getPrice() + $tax / $item->getQty());
    }

    /**
     * @param Item $item
     * @return string
     */
    public function displayRowTotalWithDiscountInclTax($item)
    {
        $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        return $this->formatPrice($item->getRowTotal() - $item->getDiscountAmount() + $tax);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getInclExclTaxMessage()
    {
        if ($this->taxData->priceIncludesTax($this->getStore())) {
            return __('Enter custom price including tax');
        } else {
            return __('Enter custom price excluding tax');
        }
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('quote.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData($options)->toHtml();
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isMoveToWishlistAllowed($item)
    {
        return $item->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
     */
    public function getCustomerWishlists()
    {
        return $this->wishlistFactory->create()->getCollection()->filterByCustomerId($this->getCustomerId());
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getItemUnitPriceHtml(Item $item)
    {
        $block = $this->getLayout()->getBlock('item_unit_price');
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getItemRowTotalHtml(Item $item)
    {
        return $this->formatPrice($item->getRowTotal());
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getItemInclTotalHtml(Item $item)
    {
        return $this->formatPrice($item->getRowTotalInclTax());
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getItemRowTotalWithDiscountHtml(Item $item)
    {
        $rowTotalWithoutDiscount = $item->getRowTotal() - $item->getTotalDiscountAmount();
        return $this->formatPrice(max(0, $rowTotalWithoutDiscount));
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getItemInclTotalWithDiscountHtml(Item $item)
    {
        $rowTotalWithoutDiscount = $item->getRowTotalInclTax() - $item->getTotalDiscountAmount();
        return $this->formatPrice(max(0, $rowTotalWithoutDiscount));
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function getItemQuestion(\Magento\Quote\Model\Quote\Item $item)
    {
        try{
            set_error_handler(
                function ($errno, $errstr) {
                    restore_error_handler();
                    throw new \InvalidArgumentException('Unable to unserialize value, string is corrupted.');
                },
                E_NOTICE
            );
            $itemNotes = $this->serializer->unserialize($item->getAdditionalData());
            restore_error_handler();
            return isset($itemNotes['customer_note']) ? $itemNotes['customer_note'] : '';
        }catch (\InvalidArgumentException $e) {

        }
        return '';
    }

    /**
     * @param Item $item
     * @return mixed
     */
    public function getItemAnswer(\Magento\Quote\Model\Quote\Item $item)
    {
        $itemNotes = $this->serializer->unserialize($item->getAdditionalData());
        return isset($itemNotes['admin_note']) ? $itemNotes['admin_note'] : '';
    }

    /**
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->taxConfig->priceIncludesTax();
    }
}
