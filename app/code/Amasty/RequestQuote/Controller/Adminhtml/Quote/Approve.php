<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Amasty\RequestQuote\Model\Source\Status;

class Approve extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{

    const ADMIN_RESOURCE = 'Amasty_RequestQuote::approve';

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('You have not approved the quote.'));
            return $resultRedirect->setPath('amasty_quote/*/');
        }
        $quote = $this->initQuote();
        if ($quote) {
            try {
                if ($expDays = $this->configHelper->getExpirationTime()) {
                    $quote->setExpiredDate($this->dateHelper->increaseDays($expDays));
                }
                if ($remDays = $this->configHelper->getReminderTime()) {
                    $quote->setReminderDate($this->dateHelper->increaseDays($remDays));
                }

                if ($quote->getStatus() == Status::ADMIN_CREATED) {
                    $newQuote = true;
                } else {
                    $newQuote = false;
                }
                $quote->setStatus(Status::APPROVED);
                $this->quoteRepository->save($quote);

                if ($newQuote) {
                    $this->emailSender->sendAdminQuoteEmail($quote);
                } else {
                    $this->emailSender->sendApproveEmail($quote);
                }

                $this->messageManager->addSuccessMessage(__('You approved the quote.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not approved the quote.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
            return $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $quote->getId()]);
        }
        return $resultRedirect->setPath('amasty_quote/*/');
    }
}
