<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart\Quote;

use Amasty\RequestQuote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\View\Element\Template;

class Move extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Amasty_RequestQuote::cart/quote/move_link.phtml';

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $helperData;

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Session
     */
    private $quoteSession;

    public function __construct(
        QuoteResource $quoteResource,
        \Magento\Checkout\Model\Session $quoteSession,
        \Amasty\RequestQuote\Helper\Data $helperData,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->quoteResource = $quoteResource;
        $this->quoteSession = $quoteSession;
    }

    public function getMoveUrl()
    {
        return $this->_urlBuilder->getUrl('amasty_quote/move/inQuote');
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        $result = '';
        if ($this->helperData->isAllowedCustomerGroup()
            && $this->helperData->isActive()
            && !$this->quoteResource->isAmastyQuote($this->quoteSession->getQuoteId())
        ) {
            $result = parent::toHtml();
        }

        return $result;
    }
}
