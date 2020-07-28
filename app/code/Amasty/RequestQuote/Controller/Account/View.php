<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Account;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class View extends AbstractAccount
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($quote = $this->loadQuote()) {
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $result->getConfig()->getTitle()->set(__('Quote # %1', $quote->getIncrementId()));

            $navigationBlock = $result->getLayout()->getBlock(
                'customer_account_navigation'
            );
            if ($navigationBlock) {
                $navigationBlock->setActive('amasty_quote/account/view');
            }
        } else {
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $result->setUrl($this->_url->getUrl('*/*/index'));
        }

        return $result;
    }
}
