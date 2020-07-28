<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Items\Column;

use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteItem;

class DefaultColumn extends \Amasty\RequestQuote\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    private $optionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        array $data = []
    ) {
        $this->optionFactory = $optionFactory;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $quoteSession, $currencyFactory, $data);
    }

    /**
     * @return QuoteItem
     */
    public function getItem()
    {
        $item = $this->_getData('item');
        if ($item instanceof Item || $item instanceof QuoteItem) {
            return $item;
        } else {
            return $item->getOrderItem();
        }
    }

    /**
     * @return array
     */
    public function getOrderOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        $default = isset($optionInfo['value']) ? $optionInfo['value'] : '';
        if (isset($optionInfo['option_type'])) {
            try {
                $group = $this->optionFactory->create()->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (\Exception $e) {
                return $default;
            }
        }
        return $default;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getItem()->getSku();
    }

    /**
     * @param QuoteItem $item
     * @return mixed
     */
    public function getTotalAmount($item)
    {
        return $item->getRowTotal() - $item->getDiscountAmount();
    }

    /**
     * @param QuoteItem $item
     * @return mixed
     */
    public function getBaseTotalAmount($item)
    {
        return $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
    }
}
