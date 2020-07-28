<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Account;

use Amasty\RequestQuote\Helper\Data;
use Amasty\RequestQuote\Model\Email\Sender;
use Amasty\RequestQuote\Model\QuoteFactory;
use Amasty\RequestQuote\Model\Quote;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

abstract class AbstractAccount extends \Magento\Framework\App\Action\Action
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var null|Quote
     */
    private $quote = null;

    /**
     * @var Sender
     */
    private $emailSender;

    /**
     * @var SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        QuoteFactory $quoteFactory,
        Registry $registry,
        Sender $emailSender,
        SessionFactory $customerSessionFactory,
        Data $helper,
        Context $context
    ) {
        parent::__construct($context);
        $this->quoteFactory = $quoteFactory;
        $this->registry = $registry;
        $this->emailSender = $emailSender;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->helper = $helper;
    }

    /**
     * @return bool|Quote
     */
    protected function loadQuote()
    {
        $result = false;
        if ($quoteId = (int)$this->_request->getParam('quote_id', null)) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            if ($quote->getId()) {
                $this->quote = $quote;
                $this->registry->unregister('amasty_quote');
                $this->registry->register('amasty_quote', $quote);
                $result = $quote;
            }
        }

        return $result;
    }

    /**
     * @return Quote|bool|null
     */
    protected function getQuote()
    {
        if ($this->quote === null) {
            $this->quote = $this->loadQuote();
        }

        return $this->quote;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function getCustomerSession()
    {
        return $this->customerSessionFactory->create();
    }

    /**
     * @return Sender
     */
    protected function getEmailSender()
    {
        return $this->emailSender;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->helper->isActive() ||
            !($auth = $this->getCustomerSession()->authenticate()) ||
            ($this->getQuote() &&
                $this->getCustomerSession()->getCustomerId() != $this->getQuote()->getCustomerId())
        ) {
            $this->getActionFlag()->set('', 'no-dispatch', true);
            if ($auth) {
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        }

        return parent::dispatch($request);
    }
}
