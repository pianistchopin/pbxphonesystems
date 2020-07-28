<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Header extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $sessionQuote, $priceCurrency, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_getSession()->getQuote()->getId()) {
            return __('Edit Quote #%1', $this->_getSession()->getQuote()->getIncrementId());
        }
        $out = $this->_getCreateQuoteTitle();

        return $this->escapeHtml($out);
    }

    /**
     * @return string
     */
    protected function _getCreateQuoteTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out .= __(
                'Create New Quote for %1 in %2',
                $this->_getCustomerName($customerId),
                $this->getStore()->getName()
            );
            return $out;
        } elseif (!$customerId && $storeId) {
            $out .= __('Create New Quote in %1', $this->getStore()->getName());

            return $out;
        } elseif ($customerId && !$storeId) {
            $out .= __('Create New Quote for %1', $this->_getCustomerName($customerId));

            return $out;
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create New Quote for New Customer');

            return $out;
        }

        return $out;
    }

    /**
     * @param int $customerId
     * @return string
     */
    protected function _getCustomerName($customerId)
    {
        $customerData = $this->customerRepository->getById($customerId);
        return $this->_customerViewHelper->getCustomerName($customerData);
    }
}
