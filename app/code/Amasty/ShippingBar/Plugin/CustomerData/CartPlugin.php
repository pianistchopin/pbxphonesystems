<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Plugin\CustomerData;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Checkout\Model\Session;

class CartPlugin
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    private $quote = null;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Session\Proxy $checkoutSession,
        ProductMetadataInterface $productMetadata
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productMetadata = $productMetadata;
    }

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '<')) {
            $totals = $this->getQuote()->getTotals();
            $subtotalAmount = $totals['subtotal']->getValue();

            $result['subtotalAmount'] = $subtotalAmount;
        }

        return $result;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }
}
