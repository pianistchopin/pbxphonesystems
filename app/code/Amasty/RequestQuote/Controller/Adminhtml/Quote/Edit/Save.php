<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

class Save extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if the creation of a new customer is allowed
            if (!$this->getSession()->getCustomerId()
                && !$this->getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }
            if ($this->initQuote(true)) {
                $this->processActionData('save');

                $quote = $this->getQuoteEditModel()
                    ->setIsValidate(true)
                    ->importPostData($this->getRequest()->getPost('quote'))
                    ->saveFromQuote();
                $this->getSession()->clearStorage();
                $this->messageManager->addSuccessMessage(__('You updated the quote.'));
                $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $quote->getId()]);
            } else {
                $resultRedirect->setPath(
                    'amasty_quote/quote/view',
                    ['quote_id' => $this->getRequest()->getParam('quote_id')]
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->getSession()->setCustomerId($this->getSession()->getQuote()->getCustomerId());
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
            $resultRedirect->setPath('amasty_quote/*/');
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('amasty_quote/*/');
        }
        return $resultRedirect;
    }
}
