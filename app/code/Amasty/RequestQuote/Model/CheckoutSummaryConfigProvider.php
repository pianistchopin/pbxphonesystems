<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutSummaryConfigProvider implements ConfigProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'maxCartItemsToDisplay' => $this->getMaxCartItemsToDisplay(),
            'cartUrl' => $this->urlBuilder->getUrl('checkout/cart')
        ];
    }

    /**
     * @return int
     */
    private function getMaxCartItemsToDisplay()
    {
        return (int)$this->scopeConfig->getValue(
            'checkout/options/max_items_display_count',
            ScopeInterface::SCOPE_STORE
        );
    }
}
