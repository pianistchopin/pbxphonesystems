<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block;

use Magento\Customer\Model\Context;

class Cart extends \Magento\Checkout\Block\Cart
{
    /**
     * @var \Amasty\RequestQuote\Model\Quote\Session
     */
    private $amastyQuoteSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Amasty\RequestQuote\Model\Quote\Session $amastyQuoteSession,
        array $data = []
    ) {
        $this->amastyQuoteSession = $amastyQuoteSession;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $data
        );
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->amastyQuoteSession->getQuote();
    }
}
