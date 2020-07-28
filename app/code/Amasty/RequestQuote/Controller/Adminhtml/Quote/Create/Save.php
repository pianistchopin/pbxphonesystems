<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

class Save extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\Create
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $path = 'amasty_quote/*/';
        $pathParams = [];

        try {
            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Magento_Customer::manage')
                && !$this->_getSession()->getCustomerId()
                && !$this->_getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }
            $this->getQuoteEditModel()->getQuote()->setCustomerId($this->_getSession()->getCustomerId());
            $this->_processActionData('save');;

            $this->_getSession()->clearStorage();
            $this->messageManager->addSuccessMessage(__('You created the quote.'));
            if ($this->_authorization->isAllowed('Amasty_RequestQuote::view')) {
                $pathParams = ['quote_id' => $this->getQuoteEditModel()->getQuote()->getId()];
                $path = 'amasty_quote/quote/view';
            } else {
                $path = 'amasty_quote/quote/index';
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // customer can be created before place order flow is completed and should be stored in current session
            $this->_getSession()->setCustomerId((int)$this->_getSession()->getQuote()->getCustomerId());
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Quote saving error: %1', $e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath($path, $pathParams);
    }
}
