<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Account\Quote;

use Amasty\RequestQuote\Model\ResourceModel\Quote;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Amasty\RequestQuote\Model\Source\Status;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_RequestQuote::account/quote/history.phtml';

    /**
     * @var Quote\Account\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Quote\Collection|null
     */
    private $quotes = null;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var Status
     */
    private $statusConfig;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $configHelper;

    public function __construct(
        Quote\Account\CollectionFactory $collectionFactory,
        Status $statusConfig,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        SessionFactory $sessionFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->sessionFactory = $sessionFactory;
        $this->statusConfig = $statusConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * @return Quote\Collection|null
     */
    public function getQuotes()
    {
        if ($this->quotes === null
            && ($customerId = $this->getCustomerSession()->getCustomerId())
        ) {
            $this->quotes = $this->collectionFactory->create($customerId)
                ->addFieldToFilter('amasty_quote.status', [
                    'in' => $this->statusConfig->getVisibleOnFrontStatuses()
                ])
                ->setOrder('created_at', 'desc');
        }

        return $this->quotes;

    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuotes()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'amasty.quote.history.pager'
            )->setCollection(
                $this->getQuotes()
            );
            $this->setChild('pager', $pager);
            $this->getQuotes()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getViewUrl($quote)
    {
        return $this->getUrl('amasty_quote/account/view', ['quote_id' => $quote->getId()]);
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getDeleteUrl($quote)
    {
        return $this->getUrl('amasty_quote/account/delete', ['quote_id' => $quote->getId()]);
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getMoveUrl($quote)
    {
        return $this->_urlBuilder->getUrl('amasty_quote/move/inCart', ['quote_id' => $quote->getId()]);
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return bool
     */
    public function isMoveShowed($quote)
    {
        return $quote->getStatus() == Status::APPROVED;
    }

    /**
     * @return bool
     */
    public function isExpiryColumnShow()
    {
        return $this->configHelper->getExpirationTime() !== null;
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getExpiredDate($quote)
    {
        $result = __('N/A');
        if ($quote->getExpiredDate() &&
            in_array($quote->getStatus(), [Status::APPROVED, Status::EXPIRED])
        ) {
            $result = $this->formatDate(
                $quote->getExpiredDate(),
                \IntlDateFormatter::MEDIUM,
                false
            );
        }

        return $result;
    }
}
