<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Quote;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Template;
use Amasty\RequestQuote\Model\Quote\Session as QuoteSession;
use Amasty\RequestQuote\Model\Quote;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var Status
     */
    private $statusConfig;

    /**
     * @var Context
     */
    private $httpContext;

    public function __construct(
        QuoteSession $quoteSession,
        Context $httpContext,
        Status $statusConfig,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quoteSession = $quoteSession;
        $this->statusConfig = $statusConfig;
        $this->httpContext = $httpContext;
    }

    /**
     * @return void
     */
    protected function prepareBlockData()
    {
        /** @var Quote $quote */
        $quote = $this->quoteSession->getLastQuote();

        if ($quote) {
            $this->addData(
                [
                    'is_quote_visible' => $this->isVisible($quote),
                    'view_quote_url'   => $this->getUrl(
                        'amasty_quote/account/view/',
                        ['quote_id' => $quote->getId()]
                    ),
                    'can_view_quote'   => $this->canViewQuote($quote),
                    'quote_id'         => $quote->prepareIncrementId()
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('quote.success.additional.info');
    }

    /**
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    protected function isVisible($quote)
    {
        return in_array(
            $quote->getStatus(),
            $this->statusConfig->getVisibleOnFrontStatuses()
        );
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    protected function canViewQuote($quote)
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
            && $this->isVisible($quote);
    }

    /**
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
