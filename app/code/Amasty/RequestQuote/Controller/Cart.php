<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller;

use Amasty\RequestQuote\Model\Customer\Manager;
use Amasty\RequestQuote\Model\Email\AdminNotification;
use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

abstract class Cart extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Amasty\RequestQuote\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Amasty\RequestQuote\Model\Email\Sender
     */
    protected $emailSender;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $configHelper;

    /**
     * @var Authentication
     */
    private $customerAuth;

    /**
     * @var Manager
     */
    private $accountManagement;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var PhpCookieManager
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var AdminNotification
     */
    private $adminNotification;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\RequestQuote\Model\Quote\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Amasty\RequestQuote\Model\Cart $cart,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Amasty\RequestQuote\Helper\Cart $cartHelper,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        AdminNotification $adminNotification,
        Manager $customerAuth,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        AuthenticationInterface $authentication,
        CookieMetadataFactory $cookieMetadataFactory,
        PhpCookieManager $cookieManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
        $this->localeResolver = $localeResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonEncoder = $encoder;
        $this->cartHelper = $cartHelper;
        parent::__construct($context);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->emailSender = $emailSender;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->priceCurrency = $priceCurrency;
        $this->configHelper = $configHelper;
        $this->customerAuth = $customerAuth;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->authentication = $authentication;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->logger = $logger;
        $this->adminNotification = $adminNotification;
    }

    /**
     * Perform customer authentication and RaQ feature state checks
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $customerSession = $this->getCustomerSession();
        if (!$this->configHelper->isGuestCanQuote()
            && !$customerSession->getCustomerId()
            && !$customerSession->authenticate()
        ) {
            $this->getActionFlag()->set('', 'no-dispatch', true);
            if (!$customerSession->getBeforeRaQUrl()) {
                $customerSession->setBeforeRaQUrl($this->_redirect->getRefererUrl());
            }
            $customerSession->setBeforeRaQRequest($request->getParams());
            $customerSession->setBeforeRequestParams($customerSession->getBeforeRaQRequest());
            $customerSession->setBeforeModuleName('amasty_quote');
            $customerSession->setBeforeControllerName('cart');
            $customerSession->setBeforeAction($request->getActionName());

            if ($request->getActionName() == 'add') {
                $this->messageManager->addErrorMessage(__('You must login or register to add items to your quote cart.'));
            }
        } elseif (!$this->configHelper->isActive() || !$this->configHelper->isAllowedCustomerGroup()) {
            $this->getActionFlag()->set('', 'no-dispatch', true);
            if ($request->getActionName() == 'add') {
                $this->messageManager->addErrorMessage(
                    'The quotation is disabled for your customer group. Please contact us for details.'
                );
            }
            $this->_response->setRedirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Session
     */
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @return \Amasty\RequestQuote\Helper\Data
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @param null|string $backUrl
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($backUrl || $backUrl = $this->getBackUrl($this->_redirect->getRefererUrl())) {
            $resultRedirect->setUrl($backUrl);
        }
        
        return $resultRedirect;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function _isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        /** @var $store \Magento\Store\Model\Store */
        $store = $this->storeManager->getStore();
        $unsecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos($url, $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)) === 0;
        return $unsecure || $secure;
    }

    /**
     * @param null $defaultUrl
     *
     * @return mixed|null|string
     */
    protected function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();
            return $returnUrl;
        }

        return $defaultUrl;
    }

    /**
     * @return \Zend_Filter_LocalizedToNormalized
     */
    protected function getLocateFilter()
    {
        return new \Zend_Filter_LocalizedToNormalized(['locale' =>$this->localeResolver->getLocale()]);
    }

    /**
     * @return \Magento\Framework\Escaper
     */
    public function getEscaper()
    {
        return $this->cartHelper->getEscaper();
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function getCustomerSession()
    {
        return $this->customerSessionFactory->create();
    }

    /**
     * @return Manager
     */
    public function getCustomerAuth()
    {
        return $this->customerAuth;
    }

    /**
     * @return AccountManagementInterface
     */
    public function getAccountManagement()
    {
        return $this->accountManagement;
    }

    /**
     * @return CustomerUrl
     */
    public function getCustomerUrl()
    {
        return $this->customerUrl;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return PhpCookieManager
     */
    public function getCookieManager()
    {
        return $this->cookieManager;
    }

    /**
     * @return CookieMetadataFactory
     */
    public function getCookieMetadataFactory()
    {
        return $this->cookieMetadataFactory;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return AdminNotification
     */
    public function getAdminNotification()
    {
        return $this->adminNotification;
    }
}
