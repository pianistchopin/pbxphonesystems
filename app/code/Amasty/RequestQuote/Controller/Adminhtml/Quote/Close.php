<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Amasty\RequestQuote\Model\Source\Status;

class Close extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::close';

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('You have not close the quote.'));
            return $resultRedirect->setPath('amasty_quote/*/');
        }
        $quote = $this->initQuote();
        if ($quote) {
            try {
                $quote->setStatus(Status::CANCELED);
                $this->quoteRepository->save($quote);

                $this->emailSender->sendDeclineEmail($quote);

                $this->messageManager->addSuccessMessage(__('You closed the quote.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not closed the quote.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
            return $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $quote->getId()]);
        }
        return $resultRedirect->setPath('amasty_quote/*/');
    }
}
