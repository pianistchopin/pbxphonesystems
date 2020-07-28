<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class ActionAbstract extends \Amasty\RequestQuote\Controller\Adminhtml\Quote
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
    protected $quoteSession;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Edit
     */
    protected $quoteEditModel;

    /**
     * @var \Amasty\RequestQuote\Model\Email\Sender
     */
    protected $emailSender;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    protected $configHelper;

    /**
     * @var \Amasty\RequestQuote\Helper\Date
     */
    protected $dateHelper;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        QuoteRepositoryInterface $quoteRepository,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        LoggerInterface $logger,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        ForwardFactory $resultForwardFactory,
        \Amasty\RequestQuote\Model\Quote\Backend\Edit $editModel,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        \Amasty\RequestQuote\Helper\Date $dateHelper
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $quoteRepository,
            $quoteSession,
            $logger
        );
        $productHelper->setSkipSaleableCheck(true);
        $this->escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->quoteSession = $quoteSession;
        $this->backendSession = $context->getSession();
        $this->quoteEditModel = $editModel;
        $this->emailSender = $emailSender;
        $this->configHelper = $configHelper;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    public function getSession()
    {
        return $this->quoteSession;
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Edit
     */
    public function getQuoteEditModel()
    {
        return $this->quoteEditModel;
    }

    /**
     * @return $this
     */
    protected function initSession()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->getSession()->setCustomerId((int)$customerId);
        }

        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->getSession()->setStoreId((int)$storeId);
        }

        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->getSession()->setCurrencyId((string)$currencyId);
            $this->getQuoteEditModel()->setRecollect(true);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function processData()
    {
        return $this->processActionData();
    }

    /**
     * @param string $action
     * @return $this
     */
    protected function processActionData($action = null)
    {
        if ($data = $this->getRequest()->getPost('quote')) {
            $this->getQuoteEditModel()->importPostData($data);
        }

        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->getQuoteEditModel()->addProduct($productId, $this->getRequest()->getPostValue());
        }

        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')
        ) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->processFiles($items);
            $this->getQuoteEditModel()->addProducts($items);
        }

        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->processFiles($items);
            $this->getQuoteEditModel()->updateQuoteItems($items);
        }

        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getQuoteEditModel()->removeItem($removeItemId, $removeFrom);
            $this->getQuoteEditModel()->recollectCart();
        }

        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        $moveQty = (int)$this->getRequest()->getPost('qty');
        if ($moveItemId && $moveTo) {
            $this->getQuoteEditModel()->moveQuoteItem($moveItemId, $moveTo, $moveQty);
        }

        $this->getQuoteEditModel()->saveQuote();

        return $this;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function processFiles($items)
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
    protected function reloadQuote()
    {
        $this->getSession()->reloadQuote();
        return $this;
    }
}
