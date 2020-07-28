<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Email;

use Amasty\RequestQuote\Helper\Data;
use Amasty\RequestQuote\Helper\Date as DateHelper;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Sender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var Emulation
     */
    private $storeEmulation;

    /**
     * @var DateHelper
     */
    private $dateHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Data $helper,
        DateHelper $dateHelper,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        SessionFactory $customerSessionFactory,
        Emulation $storeEmulation,
        Registry $registry,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->storeEmulation = $storeEmulation;
        $this->dateHelper = $dateHelper;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $configPath Ex: amasty_request_quote/admin_notifications/notify_template
     * @param string $sendTo
     * @param array $data
     */
    public function sendEmail($configPath, $sendTo = null, $data = [])
    {
        $senderEmail = null;
        $configParts = explode('/', $configPath);

        if (isset($configParts[1])) {
            $senderEmail = $this->helper->getSenderEmail($configParts[1]);

            if (!$sendTo) {
                $sendTo = $this->helper->getSendToEmail($configParts[1]);

                if ($sendTo && strpos($sendTo, ',') !== false) {
                    $sendTo = explode(',', $sendTo);
                }
            }
        }

        if ($senderEmail && $sendTo) {
            $store = $this->storeManager->getStore();
            $defaultData = [
                'store' => $store,
                'customerName' => $this->getCustomerSession()->getCustomer()->getName()
            ];
            $mailTemplateId = $this->scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) ?: str_replace('/', '_', $configPath);

            try {
                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $mailTemplateId
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
                )->setTemplateVars(
                    array_merge($defaultData, $data)
                )->setFrom(
                    $senderEmail
                )->addTo(
                    $sendTo
                );
                $transport->getTransport()->sendMessage();
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->customerSessionFactory->create();
    }

    /**
     * @param $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, array $params = [])
    {
        return $this->helper->getUrl($route, $params);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $emailTemplate
     * @return $this
     */
    private function sendQuoteEmail(\Magento\Quote\Model\Quote $quote, $emailTemplate)
    {
        $this->storeEmulation->startEnvironmentEmulation($quote->getStoreId());
        $this->helper->clearScopeUrl();
        $this->sendEmail(
            $emailTemplate,
            $quote->getCustomerEmail(),
            [
                'viewUrl' => $this->getUrl(
                    'amasty_quote/account/view',
                    ['quote_id' => $quote->getId()]
                ),
                'quote' => $quote,
                'customerName' => $quote->getCustomerName(),
                'store' => $quote->getStore(),
                'expiredDate' => $this->getExpiredDate($quote)
            ]
        );
        $this->storeEmulation->stopEnvironmentEmulation();
        $this->helper->clearScopeUrl();

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendQuoteEditEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_EDIT_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendNewQuoteEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_NEW_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendAdminQuoteEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_NEW_FROM_ADMIN_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendApproveEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_APPROVE_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendDeclineEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_CANCEL_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendExpiredEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_EXPIRED_EMAIL);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function sendReminderEmail(\Magento\Quote\Model\Quote $quote)
    {
        $this->registry->register('amasty_quote_currency', $quote->getQuoteCurrencyCode());
        $this->sendQuoteEmail($quote, \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_REMINDER_EMAIL);
        $this->registry->unregister('amasty_quote_currency');

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return null|string
     */
    private function getExpiredDate(\Magento\Quote\Model\Quote $quote)
    {
        $result = null;

        if ($this->helper->getExpirationTime() !== null && $quote->getExpiredDate()) {
            $result = $this->dateHelper->formatDate($quote->getExpiredDate());
        }

        return $result;
    }
}
