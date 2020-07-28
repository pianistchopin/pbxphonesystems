<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Quote\Item;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\DataObject\Factory as ObjectFactory;
use Magento\Quote\Model\Quote\Item;
use Zend\Code\Exception\InvalidArgumentException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Updater
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        ProductFactory $productFactory,
        FormatInterface $localeFormat,
        ObjectFactory $objectFactory,
        \Amasty\Base\Model\Serializer $serializer,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->productFactory = $productFactory;
        $this->localeFormat = $localeFormat;
        $this->objectFactory = $objectFactory;
        $this->priceCurrency = $priceCurrency;
        $this->serializer = $serializer;
    }

    /**
     * @param Item $item
     * @param array $info
     * @throws InvalidArgumentException
     * @return Updater
     */
    public function update(Item $item, array $info)
    {
        if (!isset($info['qty'])) {
            throw new InvalidArgumentException(__('The qty value is required to update quote item.'));
        }
        if (!isset($info['price'])) {
            throw new InvalidArgumentException(__('The price value is required to update quote item.'));
        }
        $itemQty = $info['qty'];
        if ($item->getProduct()->getStockItem()) {
            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                $itemQty = (int)$info['qty'];
            } else {
                $item->setIsQtyDecimal(1);
            }
        }
        $itemQty = $itemQty > 0 ? $itemQty : 1;
        $this->setPrice($info, $item);
        $this->setItemNote($info, $item);
        if (empty($info['action']) || !empty($info['configured'])) {
            $item->setQty($itemQty);
            $item->setNoDiscount(true);
            $item->getProduct()->setIsSuperMode(true);
            $item->getProduct()->unsSkipCheckRequiredOption();
            $item->checkData();
        }

        return $this;
    }

    /**
     * @param array $info
     * @param Item $item
     * @return void
     */
    private function setPrice(array $info, Item $item)
    {
        $itemPrice = $this->parsePrice($info['price'], $item);
        /** @var \Magento\Framework\DataObject $infoBuyRequest */
        $infoBuyRequest = $item->getBuyRequest();
        if ($infoBuyRequest) {
            $infoBuyRequest->setPrice($itemPrice);

            $infoBuyRequest->setValue($this->serializer->serialize($infoBuyRequest->getData()));
            $infoBuyRequest->setCode('info_buyRequest');
            $infoBuyRequest->setProduct($item->getProduct());

            $item->addOption($infoBuyRequest);
        }
        $item->setData('custom_price', $itemPrice);
        $item->setData('original_custom_price', $itemPrice);
    }

    /**
     * @param float|int $price
     * @param Item $item
     * @return float|int
     */
    private function parsePrice($price, Item $item)
    {
        $price = $this->localeFormat->getNumber($price);

        $quote = $item->getQuote();
        if ($quote->getQuoteCurrencyCode() && $quote->getQuoteCurrencyCode() != $quote->getBaseCurrencyCode()) {
            $rate = $quote->getStore()->getBaseCurrency()->getRate(
                $this->priceCurrency->getCurrency(null, $quote->getQuoteCurrencyCode())
            );
            if ($rate != 1) {
                $price = (float)$price / (float)$rate;
            }
        }

        return $price > 0 ? $price : 0;
    }

    /**
     * @param array $info
     * @param Item $item
     * @return $this
     */
    private function setItemNote(array $info, Item $item)
    {
        if (isset($info['note'])) {
            $itemNote = $this->serializer->unserialize($item->getAdditionalData()) ?: [];
            $itemNote['admin_note'] = trim($info['note']);
            $item->setAdditionalData($this->serializer->serialize($itemNote));
        }
        return $this;
    }
}
