<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Account;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Amasty\RequestQuote\Model\Source\Status;
use Amasty\RequestQuote\Helper\Data;

class Cancel extends AbstractAccount
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $this->loadQuote()->setStatus(Status::CANCELED)->save();
            $this->notifyCustomer();
            $this->messageManager->addSuccessMessage(__('Request Quote closed'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Can\'t close Request Quote'));
        }
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setUrl($this->_redirect->getRefererUrl());

        return $result;
    }

    /**
     * @return void
     */
    private function notifyCustomer()
    {
        $this->getEmailSender()->sendEmail(
            Data::CONFIG_PATH_CUSTOMER_CANCEL_EMAIL,
            $this->getCustomerSession()->getCustomer()->getEmail(),
            [
                'viewUrl' => $this->_url->getUrl(
                    'amasty_quote/account/view',
                    ['quote_id' => $this->getQuote()->getId()]
                ),
                'quote' => $this->getQuote()
            ]
        );
    }
}
