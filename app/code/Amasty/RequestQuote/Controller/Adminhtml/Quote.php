<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml;

use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

abstract class Quote extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::manage_quotes';

    /**
     * @var string[]
     */
    protected $_publicActions = ['view', 'index'];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $translateInline;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    protected $quoteSession;

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
        LoggerInterface $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteSession = $quoteSession;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_RequestQuote::manage_quotes');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));
        return $resultPage;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function initQuote($editMode = false)
    {
        $id = $this->getRequest()->getParam('quote_id');
        try {
            if ($editMode && $this->quoteSession->getChildId($id)) {
                $quote = $this->quoteRepository->getMagentoQuote(
                    $this->quoteSession->getChildId($id),
                    ['*']
                );
            } else {
                $quote = $this->quoteRepository->get($id, ['*']);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        if ($editMode && !$this->validateSession($quote)) {
            $this->messageManager->addErrorMessage(__('Your session has been expired. Please try again'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        $this->_coreRegistry->register('amasty_quote', $quote);
        $this->_coreRegistry->register('current_quote', $quote);
        $this->quoteSession->setQuote($quote);
        $this->quoteSession->setCurrencyCode($quote->getQuoteCurrencyCode());

        return $quote;
    }

    /**
     * @param $quote
     * @return bool
     */
    public function validateSession($quote)
    {
        return $this->getSession()->getQuoteId() && $quote->getId() == $this->getSession()->getQuoteId();
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    public function getSession()
    {
        return $this->quoteSession;
    }

    /**
     * @return bool
     */
    protected function isValidPostRequest()
    {
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        return ($formKeyIsValid && $isPost);
    }
}
