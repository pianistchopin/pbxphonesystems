<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

class Cancel extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\Create
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($orderId = $this->_getSession()->getReordered()) {
            $this->_getSession()->clearStorage();
            $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $orderId]);
        } else {
            $this->_getSession()->clearStorage();
            $resultRedirect->setPath('amasty_quote/*');
        }
        return $resultRedirect;
    }
}
