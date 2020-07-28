<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit;

use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;

class Info extends \Amasty\RequestQuote\Block\Adminhtml\Quote\AbstractQuote
{
    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    private $metadata;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    private $metadataElementFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->metadataElementFactory = $elementFactory;
        parent::__construct($context, $quoteSession, $adminHelper, $configHelper, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please correct the parent block for this block.')
            );
        }
        $this->setQuote($this->getParentBlock()->getQuote());

        foreach ($this->getParentBlock()->getQuoteInfoData() as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }

        parent::_beforeToHtml();
    }

    /**
     * @return null|string
     */
    public function getQuoteStoreName()
    {
        if ($this->getQuote()) {
            $storeId = $this->getQuote()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getQuote()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getQuote()) {
            $customerGroupId = $this->getQuote()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if ($this->getQuote()->getCustomerIsGuest() || !$this->getQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $this->getQuote()->getCustomerId()]);
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * @param array $data
     * @param int $sortQuote
     * @return int
     */
    protected function _prepareAccountDataSortQuote(array $data, $sortQuote)
    {
        if (isset($data[$sortQuote])) {
            return $this->_prepareAccountDataSortQuote($data, $sortQuote + 1);
        }

        return $sortQuote;
    }

    /**
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = [];
        $entityType = 'customer';

        /* @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($this->metadata->getAllAttributesMetadata($entityType) as $attribute) {
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $orderKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getQuote()->getData($orderKey);

            if ($orderValue != '') {
                $metadataElement = $this->metadataElementFactory->create($attribute, $orderValue, $entityType);
                $value = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortQuote = $attribute->getSortOrder() + $attribute->isUserDefined() ? 200 : 0;
                $sortQuote = $this->_prepareAccountDataSortQuote($accountData, $sortQuote);
                $accountData[$sortQuote] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, ['br']),
                ];
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getQuote()->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * @param mixed $store
     * @param string $createdAt
     * @return \DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * @param mixed $store
     * @return string
     */
    public function getTimezoneForStore($store)
    {
        return $this->_localeDate->getConfigTimezone(
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }

    /**
     * @param string $createdAt
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        /**
         * @TODO add address renderer
         */
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getChildHtml($alias = '', $useCache = true)
    {
        $layout = $this->getLayout();

        if ($alias || !$layout) {
            return parent::getChildHtml($alias, $useCache);
        }

        $childNames = $layout->getChildNames($this->getNameInLayout());
        $outputChildNames = array_diff($childNames, ['extra_customer_info']);

        $out = '';
        foreach ($outputChildNames as $childName) {
            $out .= $layout->renderElement($childName, $useCache);
        }

        return $out;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
        return $this->getSession()->getParentQuote();
    }
}
