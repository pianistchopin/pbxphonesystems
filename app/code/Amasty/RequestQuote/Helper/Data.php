<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Helper;

use Amasty\RequestQuote\Model\Source\Yesnocustom;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const CONFIG_PATH_IS_ACTIVE = 'amasty_request_quote/general/is_active';
    const CONFIG_PATH_DISPLAY_ON_PDP = 'amasty_request_quote/general/visible_on_pdp';
    const CONFIG_PATH_DISPLAY_ON_LISTING = 'amasty_request_quote/general/visible_on_plp';
    const CONFIG_PATH_DISPLAY_FOR_GROUP = 'amasty_request_quote/general/visible_for_groups';
    const CONFIG_PATH_INFORM_GUEST = 'amasty_request_quote/general/inform_guest';
    const ATTRIBUTE_NAME_HIDE_BUY_BUTTON = 'hide_quote_buy_button';

    // @codingStandardsIgnoreStart
    const CONFIG_PATH_ADMIN_NOTIFY_EMAIL = 'amasty_request_quote/admin_notifications/notify_template';
    const CONFIG_PATH_CUSTOMER_SUBMIT_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_submit';
    const CONFIG_PATH_CUSTOMER_APPROVE_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_approve';
    const CONFIG_PATH_CUSTOMER_NEW_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_create_quote';
    const CONFIG_PATH_CUSTOMER_EDIT_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_edit_quote';
    const CONFIG_PATH_CUSTOMER_PROMOTION_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_end_promotion';
    const CONFIG_PATH_CUSTOMER_CANCEL_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_cancel';
    const CONFIG_PATH_CUSTOMER_EXPIRED_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_expired';
    const CONFIG_PATH_CUSTOMER_REMINDER_EMAIL = 'amasty_request_quote/customer_notifications/customer_template_reminder';
    const CONFIG_PATH_CUSTOMER_NEW_FROM_ADMIN_EMAIL = 'amasty_request_quote/customer_notifications/admin_template_create_quote';
    // @codingStandardsIgnoreEnd

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->sessionFactory = $sessionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue('amasty_request_quote/' . $path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_IS_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return bool
     */
    public function displayByuButtonOnPdp()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_DISPLAY_ON_PDP, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function displayByuButtonOnListing()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_DISPLAY_ON_LISTING, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isAllowedCustomerGroup()
    {
        $customerGroupId = $this->getCustomerSession()->getCustomerGroupId();
        $allowedGroups = $this->scopeConfig->getValue(
            self::CONFIG_PATH_DISPLAY_FOR_GROUP,
            ScopeInterface::SCOPE_STORE
        );
        return in_array($customerGroupId, explode(',', $allowedGroups));
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        $customerId = $this->getCustomerSession()->getCustomerId();

        return (bool)$customerId
            && $this->getCustomerSession()->checkCustomerId($customerId);
    }

    /**
     * @return bool
     */
    public function isGuestCanQuote()
    {
        return $this->isAllowedCustomerGroup() && !$this->isLoggedIn();
    }

    /**
     * @param $quoteId
     * @return \Magento\Framework\DataObject
     */
    public function getOrderByQuoteId($quoteId)
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('quote_id', $quoteId);
        return $collection->getFirstItem();
    }

    /**
     * @param string $groupName
     *
     * @return mixed
     */
    public function getSenderEmail($groupName)
    {
        return $this->getModuleConfig($groupName . '/sender_email_identity');
    }

    /**
     * @param $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, array $params = [])
    {
        return $this->_getUrl($route, $params);
    }

    public function clearScopeUrl()
    {
        $this->_urlBuilder->setScope(null);
    }

    /**
     * @param string $groupName
     *
     * @return mixed
     */
    public function getSendToEmail($groupName)
    {
        return $this->getModuleConfig($groupName . '/send_to_email');
    }

    /**
     * @return array
     */
    public function getExcludeCategories()
    {
        return explode(',', $this->getModuleConfig('general/exclude_category'));
    }

    /**
     * @inheritdoc
     */
    public function getExpirationTime()
    {
        return $this->getModuleConfig('proposal/expiration_time');
    }

    /**
     * @inheritdoc
     */
    public function getReminderTime()
    {
        return $this->getModuleConfig('proposal/reminder_time');
    }

    /**
     * @return bool
     */
    public function isInformGuests()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_INFORM_GUEST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @inheritdoc
     */
    public function getGuestButtonText()
    {
        $buttonText = $this->getModuleConfig('general/guest_button_text');
        if (!$buttonText) {
            $buttonText = __('Login for quote');
        }

        return $buttonText;
    }

    /**
     * @return bool
     */
    public function isAdminNotificationsByCron()
    {
        return $this->getModuleConfig('admin_notifications/notify') == Yesnocustom::CUSTOM;
    }

    /**
     * @return bool
     */
    public function isAdminNotificationsInstantly()
    {
        return $this->getModuleConfig('admin_notifications/notify') == Yesnocustom::INSTANTLY;
    }
}
