<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Quote\Model\Quote\Item;

use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Amasty\RequestQuote\Model\Source\Status;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Framework\DataObject\Factory as ObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Updater as NativeUpdater;

class Updater
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        PriceCurrencyInterface $priceCurrency,
        QuoteSession $quoteSession,
        ObjectFactory $objectFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->priceCurrency = $priceCurrency;
        $this->quoteSession = $quoteSession;
        $this->objectFactory = $objectFactory;
    }

    /**
     * @param NativeUpdater $subject
     * @param Item $item
     * @param array $info
     *
     * @return array
     */
    public function beforeUpdate($subject, Item $item, $info)
    {
        if (isset($info['custom_price']) && $this->isAmastyQuote($item)) {
            $price = $info['custom_price'];
            $rate = $item->getQuote()->getStore()->getBaseCurrency()->getRate(
                $this->priceCurrency->getCurrency(null, $this->getCurrencyCode($item))
            );
            if ($rate != 1) {
                $price = (float)$price / (float)$rate;
            }
            $info['custom_price'] = $price;
            if (!$item->getOptionByCode('amasty_quote_price')) {
                $priceOption = $this->objectFactory->create(
                    []
                )->setCode(
                    'amasty_quote_price'
                )->setValue(
                    $price
                )->setProduct(
                    $item->getProduct()
                );
                $item->addOption($priceOption);
            }
        }

        return [$item, $info];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return null|string
     */
    public function getCurrencyCode(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->quoteSession->getCurrencyId() && $this->quoteSession->getCurrencyId() != 'false'
            ? $this->quoteSession->getCurrencyId()
            : $item->getQuote()->getQuoteCurrencyCode();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    private function isAmastyQuote(\Magento\Quote\Model\Quote\Item $item)
    {
        try {
            // detect amasty quote
            $this->quoteRepository->get($item->getQuote()->getId());
            $result = true;
        } catch (NoSuchEntityException $entityException) {
            $result = false;
        }

        return $result;
    }
}
