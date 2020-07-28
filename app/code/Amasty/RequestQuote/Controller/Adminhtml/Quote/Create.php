<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Create extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $session;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Edit
     */
    private $quoteEditModel;

    /**
     * @var \Magento\Quote\Model\CustomerManagement
     */
    private $customerManagement;

    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $session,
        \Amasty\RequestQuote\Model\Quote\Backend\Edit $quoteEditModel,
        \Magento\Quote\Model\CustomerManagement $customerManagement
    ) {
        parent::__construct($context);
        $productHelper->setSkipSaleableCheck(true);
        $this->escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->session = $session;
        $this->quoteEditModel = $quoteEditModel;
        $this->customerManagement = $customerManagement;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    protected function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Edit
     */
    protected function getQuoteEditModel()
    {
        return $this->quoteEditModel;
    }

    /**
     * @return $this
     */
    protected function _initSession()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int)$customerId);
        }

        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int)$storeId);
        }

        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string)$currencyId);
            $this->getQuoteEditModel()->setRecollect(true);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _processData()
    {
        return $this->_processActionData();
    }

    /**
     * @param string $action
     * @return $this
     */
    protected function _processActionData($action = null)
    {
        $currencyId = $this->_getSession()->getStore()->getCurrentCurrency()->getCode();

        $this->getQuoteEditModel()->getQuote()->setQuoteCurrencyCode((string)$currencyId);

        $this->getQuoteEditModel()->getQuote()->getBillingAddress();
        $this->getQuoteEditModel()->getQuote()->getShippingAddress();

        if ($this->getRequest()->has('item')
            && !$this->getRequest()->getPost('update_items')
            && !($action == 'save')
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->getQuoteEditModel()->addProducts($items);
        }

        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $this->getQuoteEditModel()->updateQuoteItems($items);
        }

        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        if ($removeItemId) {
            $this->getQuoteEditModel()->removeItem($removeItemId);
            $this->getQuoteEditModel()->recollectQuote();
        }

        if ($action == 'save') {
            $this->getQuoteEditModel()->getQuote()->setStatus(\Amasty\RequestQuote\Model\Source\Status::ADMIN_CREATED);
            $quote = $this->getQuoteEditModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('quote'))
                ->saveFromQuote();
            $this->customerManagement->populateCustomerInfo($quote);
        } else {
            $this->getQuoteEditModel()->saveQuote();
        }

        return $this;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /* @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Product::class);
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }

    /**
     * @return $this
     */
    protected function _reloadQuote()
    {
        $id = $this->getQuote()->getId();
        $this->getQuote()->load($id);
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->getAclResource());
    }

    /**
     * @return string
     */
    protected function getAclResource()
    {
        $action = strtolower($this->getRequest()->getActionName());

        switch ($action) {
            case 'index':
            case 'save':
                $aclResource = 'Amasty_RequestQuote::create';
                break;
            case 'cancel':
            case 'close':
                $aclResource = 'Amasty_RequestQuote::close';
                break;
            default:
                $aclResource = 'Amasty_RequestQuote::actions';
                break;
        }

        return $aclResource;
    }
}
