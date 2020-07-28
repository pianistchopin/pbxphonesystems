<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Helper;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\ScopeInterface;
use Amasty\Stockstatus\Model\Source\BackOrder;
use Amasty\Stockstatus\Model\Backend\UpdaterAttribute;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * 1 Day is 24*60*60 = 86400sec;
     */
    const ONE_DAY = 86400;

    /**
     * @var array
     */
    private $cachedStatuses = null;

    /**
     * @var array
     */
    private $statusId = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var \Amasty\Stockstatus\Model\RangesFactory
     */
    private $rangesFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\OptionManagementFactory
     */
    private $optionManagementFactory;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $pageHelper;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var null|string
     */
    private $infoBlock = null;

    /**
     * @var \Amasty\Stockstatus\Model\ResourceModel\Inventory
     */
    private $inventoryManager;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Amasty\Stockstatus\Helper\Image $imageHelper,
        \Amasty\Stockstatus\Model\RangesFactory $rangesFactory,
        \Magento\Catalog\Model\Product\Attribute\OptionManagementFactory $optionManagementFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Cms\Helper\Page $pageHelper,
        DateTime $dateTime,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Escaper $escaper,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Stockstatus\Model\ResourceModel\Inventory $inventoryManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeDate = $localeDate;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->stockRegistry = $stockRegistry;
        $this->imageHelper = $imageHelper;
        $this->rangesFactory = $rangesFactory;
        $this->optionManagementFactory = $optionManagementFactory;
        $this->layout = $layout;
        $this->pageHelper = $pageHelper;
        $this->dateTime = $dateTime;
        $this->priceCurrency = $priceCurrency;
        $this->moduleManager = $context->getModuleManager();
        $this->escaper = $escaper;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->inventoryManager = $inventoryManager;
    }

    public function getRulesEnabled()
    {
        return $this->getModuleConfig('general/use_range_rules');
    }

    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue('amstockstatus/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getStockAlert(Product $product)
    {
        $html = '';
        if ($this->moduleManager->isEnabled('Amasty_Xnotif')) {
            $configurableProduct = $this->registry->registry('current_product');
            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $product);

            $alertBlock = $this->layout->getBlock(
                'productalert.stock'
            );
            if ($alertBlock) {
                $alertBlock->setData('parent_product_id', $configurableProduct->getId());
                $alertBlock->setOriginalProduct($product);
                $alertBlock->setTemplate('Magento_ProductAlert::product/view.phtml');
                $html = $alertBlock->toHtml();
            }

            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $configurableProduct);
        }

        return $html;
    }

    public function getPriceAlert(Product $product)
    {
        $html = '';
        if ($this->moduleManager->isEnabled('Amasty_Xnotif')) {
            $configurableProduct = $this->registry->registry('current_product');
            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $product);

            $alertBlock = $this->layout->getBlock(
                'productalert.price'
            );
            if ($alertBlock) {
                $alertBlock->setData('parent_product_id', $configurableProduct->getId());
                $alertBlock->setOriginalProduct($product);
                $alertBlock->setTemplate('Magento_ProductAlert::product/view.phtml');
                $html = $alertBlock->toHtml();
            }

            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $configurableProduct);
        }

        return $html;
    }

    /**
     * @param $product
     * @param bool $addWrapper
     * @param bool $isProductList
     * @param $storeId
     * @return \Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function showStockStatus(
        $product,
        $addWrapper = false,
        $isProductList = false,
        $storeId = null
    ) {
        if ($storeId !== null) {
            $this->storeManager->setCurrentStore($storeId);
        }
        $status = $this->getCustomStockStatus($product);
        if (!$status) {
            return "";
        }

        if ($isProductList || !$this->getModuleConfig('general/display_default_status')) {
            $result = '';
        } else {
            $result = $this->getDefaultStockStatus($product);
        }

        $result = $result . ' ' . $status;

        if ($addWrapper) {
            $result = '<div class="stock">' . $result . '</div>';
        }

        return $result;
    }

    /**
     * @param $product
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDefaultStockStatus($product)
    {
        $stockStatus = $this->stockRegistry->getStockStatus(
            $product->getId(),
            $this->storeManager->getWebsite()->getId()
        );
        if ($stockStatus->getStockStatus()) {
            $result = __('In stock');
        } else {
            $result = __('Out of stock');
        }

        return $result;
    }

    public function getStatusIconImage($product = null)
    {
        list($productId, $storeId) = $product
            ? [$product->getId(), $product->getStoreId()]
            : [null, null];

        $iconUrl = $this->imageHelper->getStatusIconUrl(
            $this->getCustomStockStatusId($productId),
            $storeId
        );
        if ($iconUrl) {
            $iconUrl = '<img src="' . $iconUrl . '" class="amstockstatus_icon" alt="" title="">';
        }

        return $iconUrl;
    }

    /**
     * @param Product $product
     * @return string
     */
    private function getCustomStockStatus(Product $product)
    {
        $result = "";
        if (!$this->getModuleConfig('general/displayforoutonly') || !$product->getIsSalable()) {
            $status = $this->getCachedStockStatus($product->getId());
            if ($status === null) {
                $status = $this->getCustomStockStatusText($product);
            }

            if ($status) {
                $result = '<span class="amstockstatus amsts_' . $this->getCustomStockStatusId($product->getId()) . '">'
                    . $status
                    . '</span>';

                $imageHtml = $this->getStatusIconImage($product);
                if ($this->getModuleConfig('general/icon_only')) {
                    $result = $imageHtml;
                } else {
                    $result = $imageHtml . $result;
                }
            }
        }

        return $result;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getCustomStockStatusText(Product $product)
    {
        if (!$product || !$product->getId()) {
            return false;
        }

        $status          = '';
        $quantity        = null;

        if ($product->getData('custom_stock_status_qty_based')) {
            $rules = [];
            $quantity = $this->getProductQty($product);

            //load status from our model
            $rule = ( $this->getModuleConfig('general/use_range_rules') &&
                $product->getData('custom_stock_status_qty_rule') )?
                $product->getData('custom_stock_status_qty_rule'):
                null;
            $rules[] = $rule;

            if (!$rule && $this->getModuleConfig('general/use_range_rules')) {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $backOrderValue = $stockItem->getBackorders();
                $rules[] = $backOrderValue + BackOrder::BACKORDERS_INCREMENT;
            }

            $rangeModel = $this->rangesFactory->create();
            $rangeModel->loadByQtyAndRule($quantity, $rules);

            if ($rangeModel->hasData('status_id')) {
                $this->statusId[$product->getId()] = $rangeModel->getData('status_id');

                // getting status for range
                $optionManagement = $this->optionManagementFactory->create();
                foreach ($optionManagement->getItems('custom_stock_status') as $option) {
                    if ($this->statusId[$product->getId()] == $option['value']) {
                        $status = $option['label'];
                        break;
                    }
                }
            }
        }

        if ('' == $status && !$this->getModuleConfig('general/use_ranges_only')) {
            $status = $product->getAttributeText('custom_stock_status');
            $this->statusId[$product->getId()] = $product->getData('custom_stock_status');
        }

        if (!$status) {
            return '';
        }

        $status = $this->replaceCustomVariables($status, $product);

        $status = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $status);
        $status = htmlspecialchars_decode($status);
        $this->setCachedStockStatus($product->getId(), $status);

        return $status;
    }

    /**
     * @param $status
     * @param $product
     * @return mixed
     */
    private function replaceCustomVariables($status, $product)
    {
        // search for attribute entries
        preg_match_all('@\{(.+?)\}@', $status, $matches);
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $status = $this->updateStatus($status, $product, $match);
            }
        }

        return $status;
    }

    /**
     * @param string $status
     * @param Product $product
     * @param string $variable
     * @return string
     */
    private function updateStatus($status, $product, $variable)
    {
        switch ($variable) {
            case 'qty-threshold':
                $result = $this->getProductQty($product, false);
                $result -= $this->stockRegistry->getStockItem($product->getId())->getMinQty();
                break;
            case 'qty':
                $result = $this->getProductQty($product, false);
                break;
            case 'tomorrow':
                $result = $this->getCustomDateValue(self::ONE_DAY, 1);
                break;
            case 'day-after-tomorrow':
                $result = $this->getCustomDateValue(2 * self::ONE_DAY, 1);
                break;
            case 'yesterday':
                $result = $this->getCustomDateValue(-self::ONE_DAY, 1);
                break;
            case 'expected_date':
                $value = $product->getData(UpdaterAttribute::EXPECTED_DATE_CODE);
                $result = $this->dateTime->gmtDate($this->getFormatDate(false), $value);
                if ($this->isExpectedDateHide($value)) {
                    $status = '';
                }
                break;
            default:
                $result = $this->getAttributeValue($product, $variable);
        }
        if (is_numeric($result)) {
            $result = $this->cutZeroes($result);
        }

        return str_replace('{' . $variable . '}', $result, $status);
    }

    /**
     * @param $value
     * @return bool
     */
    private function isExpectedDateHide($value)
    {
        $result = false;
        $currentDay = $this->dateTime->gmtDate($this->getFormatDate(true));
        if (!$this->getModuleConfig('expected_date/expected_date_enabled')
            || !$value
            || ($this->getModuleConfig('expected_date/expired')
                && $this->dateTime->gmtTimestamp($value) < $this->dateTime->gmtTimestamp($currentDay))
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param Product $product
     * @param string $attributeCode
     * @return float|string
     */
    private function getAttributeValue($product, $attributeCode)
    {
        $result = '';
        if ($value = $product->getData($attributeCode)) {
            $attribute = $product->getResource()->getAttribute($attributeCode);
            if ($attribute && $attribute->usesSource()) {
                $result = $attribute->getSource()->getOptionText($value);
            } elseif (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value)) {
                $result = $this->dateTime->gmtDate($this->getFormatDate(true), $value);
            } elseif ($attribute->getFrontendInput() == 'price') {
                $result = $this->priceCurrency->format($value, false);
            } else {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * @param $time
     * @param $excludeSunday
     * @return string
     */
    private function getCustomDateValue($time, $excludeSunday)
    {
        if ($excludeSunday && date('w', time() + $time) == 0) {
            $time += self::ONE_DAY;
        }
        $value = date("d-m-Y", time() + $time);

        $value = $this->localeDate->formatDateTime(
            new \DateTime($value),
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::NONE
        );

        return $value;
    }

    /**
     * @param $id
     *
     * @return int
     */
    public function getCustomStockStatusId($id)
    {
        $result = 0;
        if (isset($this->statusId[$id])) {
            $result = $this->statusId[$id];
        }

        return $result;
    }

    /**
     * @param $product
     * @param $item
     * @return \Magento\Framework\Phrase|string
     */
    public function getProductStockStatus($product, $item)
    {
        if ($product->getTypeId() == 'configurable') {
            $product = $item->getOptionByCode('simple_product')->getProduct();
        }

        return $this->getCartStockStatus($product->getData('sku'));
    }

    /**
     * @param $productSku
     * @return \Magento\Framework\Phrase|string
     */
    public function getCartStockStatus($productSku)
    {
        try {
            $product = $this->productRepository->get($productSku);
            $status = $this->showStockStatus($product, 1, 0);
        } catch (NoSuchEntityException $exception) {
            $status = '';
        }

        return $status;
    }

    /**
     * @param Product $product
     * @param bool $withoutTreshold
     * @return int
     */
    private function getProductQty(Product $product, $withoutTreshold = true)
    {
        switch ($product->getTypeId()) {
            case Configurable::TYPE_CODE:
                //get total qty for configurable product as summ from simple
                $collection = $product->getTypeInstance(true)
                    ->getUsedProducts($product);
                $quantity = $this->getQtySum($collection);
                break;
            case Grouped::TYPE_CODE:
                //get total qty for grouped product as summ from simple
                $collection = $product->getTypeInstance(true)
                    ->getAssociatedProducts($product);
                $quantity = $this->getQtySum($collection);
                break;
            default:
                $quantity = $this->inventoryManager->getQty(
                    $product->getData('sku'),
                    $product->getStore()->getWebsite()->getCode()
                );
                break;
        }

        if ($this->getModuleConfig('general/use_threshold_for_range') && $withoutTreshold) {
            // inventory catalog already subtract minQty
            $quantity -= $this->getMinQty($product->getId());
        }

        return $quantity;
    }

    /**
     * @param array $collection
     * @return float|int
     * @throws NoSuchEntityException
     */
    private function getQtySum($collection)
    {
        $quantity = 0;
        /** @var Product $simple */
        foreach ($collection as $simple) {
            $simpleQty = $this->inventoryManager->getQty(
                $simple->getData('sku'),
                $simple->getStore()->getWebsite()->getCode()
            );

            if ($simpleQty > 0) {
                $quantity += $simpleQty;
            }
        }

        return $quantity;
    }

    /**
     * @param int $productId
     *
     * @return float
     */
    private function getMinQty($productId)
    {
        return $this->stockRegistry->getStockItem($productId)->getMinQty();
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCachedStockStatus($id)
    {
        $result = null;
        if (isset($this->cachedStatuses[$id])) {
            $result = $this->cachedStatuses[$id];
        }

        return $result;
    }

    /**
     * @param int $id
     * @param string $currentStockStatus
     */
    public function setCachedStockStatus($id, $currentStockStatus)
    {
        $this->cachedStatuses[$id] = $currentStockStatus;
    }

    public function getInfoBlock($store = 0)
    {
        if ($this->infoBlock === null) {
            $this->infoBlock = '';
            $infoText = $this->getModuleConfig('info/text');
            if ($this->getModuleConfig('info/enabled') && $infoText) {
                $infoText = $this->escaper->escapeHtml($infoText);
                /** @var \Magento\Cms\Model\Page $page */
                $page = $this->pageFactory->create();
                if ($cmsPageId = $this->getModuleConfig('info/cms')) {
                    if (!$store) {
                        $store = $this->storeManager->getStore()->getId();
                    }
                    $page->setStoreId($store);
                    $page->load($cmsPageId);
                }
                $url = $cmsPageId && $page->isActive()
                    ?
                    $this->pageHelper->getPageUrl($cmsPageId)
                    :
                    '#';
                $blank = ($url == '#') ? '' : 'target="_blank"';

                $this->infoBlock = sprintf(
                    '<a href="%s" %s class="amstockstatus-info-link">%s</a>',
                    $url,
                    $blank,
                    $infoText
                );
            }
        }

        return $this->infoBlock;
    }

    /**
     * @param boolean $default
     * @return string
     */
    private function getFormatDate($default)
    {
        $format = 'F d, Y';
        if (!$default) {
            $format = $this->getModuleConfig('expected_date/format');
        }

        return $format;
    }

    /**
     * @param $value
     * @return mixed
     */
    private function cutZeroes($value)
    {
        $regexp = '@(\d+(?:[^\\.]\d+)*)\\.0+@';
        $value = preg_replace($regexp, '$1', $value);

        return $value;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function isHidePrice($product)
    {
        $this->getCustomStockStatus($product);
        $appliedStatuses = $this->scopeConfig->getValue(
            'amasty_hide_price/stock_status/stock_status',
            ScopeInterface::SCOPE_STORE
        );

        return $appliedStatuses && in_array(
            $this->getCustomStockStatusId($product->getId()),
            explode(',', $appliedStatuses)
        );
    }

    /**
     * @return int
     */
    public function getOutofstockVisibility()
    {
        return (int)$this->getModuleConfig('configurable_products/outofstock');
    }
}
