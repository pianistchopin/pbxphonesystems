<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;

class Order extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::order';

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $mageSessionQuote;

    /**
     * @var \Amasty\RequestQuote\Model\Quote\Backend\Session
     */
    private $amSessionQuote;

    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Session\Quote $mageSessionQuote,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $amSessionQuote
    ) {
        parent::__construct($context);
        $this->mageSessionQuote = $mageSessionQuote;
        $this->amSessionQuote = $amSessionQuote;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('You can not create the order.'));
            return $resultRedirect->setPath('amasty_quote/*/');
        }
        $quote = $this->amSessionQuote->getQuote();
        if ($quote) {
            try {
                $this->mageSessionQuote->setCustomerId($quote->getCustomerId());
                $this->mageSessionQuote->setStoreId($quote->getStoreId());
                $this->mageSessionQuote->setCurrencyId($quote->getQuoteCurrencyCode());
                $this->mageSessionQuote->setQuoteId($quote->getId());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not closed the quote.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
            return $resultRedirect->setPath('sales/order_create/index');
        }
        return $resultRedirect->setPath('amasty_quote/*/');
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
