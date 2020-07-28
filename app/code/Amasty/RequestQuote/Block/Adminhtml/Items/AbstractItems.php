<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Items;

class AbstractItems extends \Magento\Backend\Block\Template
{
    const DEFAULT_TYPE = 'default';

    /**
     * @var array
     */
    private $columnRenders = [];

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $quoteSession;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    private $currency;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    private $baseCurrency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->stockConfiguration = $stockConfiguration;
        $this->quoteSession = $quoteSession;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param array $blocks
     * @return $this
     */
    public function setColumnRenders(array $blocks)
    {
        foreach ($blocks as $blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block->getRenderedBlock() === null) {
                $block->setRenderedBlock($this);
            }
            $this->columnRenders[$blockName] = $block;
        }
        return $this;
    }

    /**
     * @param string $type
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        /** @var $renderer \Amasty\RequestQuote\Block\Adminhtml\Items\AbstractItems */
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof \Magento\Framework\View\Element\BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setColumnRenders($this->getLayout()->getGroupChildNames($this->getNameInLayout(), 'column'));

        return $renderer;
    }

    /**
     * @param string $column
     * @param string $compositePart
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getColumnRenderer($column, $compositePart = '')
    {
        $column = 'column_' . $column;
        if (isset($this->columnRenders[$column . '_' . $compositePart])) {
            $column .= '_' . $compositePart;
        }
        if (!isset($this->columnRenders[$column])) {
            return false;
        }
        return $this->columnRenders[$column];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     */
    public function getItemHtml(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->getItemRenderer($item->getProductType())->setItem($item)->toHtml();
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $block = $this->getColumnRenderer($column, $item->getProductType());

        if ($block) {
            $block->setItem($item);
            if ($field !== null) {
                $block->setField($field);
            }
            return $block->toHtml();
        }
        return '&nbsp;';
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
        return $this->quoteSession->getQuote();
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface|mixed
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getQuote();
        }
        return $obj;
    }

    /**
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        return $this->displayPrices(
            $this->getPriceDataObject()->getData('base_' . $code),
            $this->getPriceDataObject()->getData($code),
            $strong,
            $separator
        );
    }

    /**
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayCustomPrice($strong = false, $separator = '<br />')
    {
        return $this->displayPrices(
            $this->getPriceDataObject()->getCustomPrice(),
            $this->getBaseCurrency()->convert($this->getPriceDataObject()->getCustomPrice(), $this->getCurrency()),
            $strong,
            $separator
        );
    }

    /**
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayProductPrice($strong = false, $separator = '<br />')
    {
        $productPrice = $this->getPriceDataObject()->getProduct()->getFinalPrice();
        return $this->displayPrices(
            $productPrice,
            $this->getBaseCurrency()->convert($productPrice, $this->getCurrency()),
            $strong,
            $separator
        );
    }

    /**
     * @param float $basePrice
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />')
    {
        return $this->displayRoundedPrices($basePrice, $price, 2, $strong, $separator);
    }

    /**
     * @param float $basePrice
     * @param float $price
     * @param int $precision
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayRoundedPrices($basePrice, $price, $precision = 2, $strong = false, $separator = '<br />')
    {
        if ($this->getQuote()->isCurrencyDifferent()) {
            $res = $this->formatBasePricePrecision($basePrice, $precision);
            $res .= $separator;
            $res .= $this->formatPricePrecision($price, $precision, true);
        } else {
            $res = $this->formatPricePrecision($price, $precision);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        }

        return $res;
    }

    /**
     * @return \Magento\Directory\Model\Currency
     */
    public function getCurrency()
    {
        if ($this->currency === null) {
            $this->currency = $this->currencyFactory->create();
            $currencyCode = $this->getQuote()->getQuoteCurrencyCode() ?: $this->getQuote()->getBaseCurrencyCode();
            $this->currency->load($currencyCode);
        }
        return $this->currency;
    }

    /**
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        if ($this->baseCurrency === null) {
            $this->baseCurrency = $this->currencyFactory->create();
            $currencyCode = $this->getQuote()->getBaseCurrencyCode();
            $this->baseCurrency->load($currencyCode);
        }
        return $this->baseCurrency;
    }

    /**
     * @param $price
     * @param $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
       return $this->getCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * @param $price
     * @param $precision
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision, [], true, false);
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayTaxCalculation(\Magento\Framework\DataObject $item)
    {
        if ($item->getTaxPercent() && $item->getTaxString() == '') {
            $percents = [$item->getTaxPercent()];
        } elseif ($item->getTaxString()) {
            $percents = explode(\Magento\Tax\Model\Config::CALCULATION_STRING_SEPARATOR, $item->getTaxString());
        } else {
            return '0%';
        }

        foreach ($percents as &$percent) {
            $percent = sprintf('%.2f%%', $percent);
        }
        return implode(' + ', $percents);
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayTaxPercent(\Magento\Framework\DataObject $item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%s%%', $item->getTaxPercent() + 0);
        } else {
            return '0%';
        }
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getQuote()->formatPrice($price);
    }
}
