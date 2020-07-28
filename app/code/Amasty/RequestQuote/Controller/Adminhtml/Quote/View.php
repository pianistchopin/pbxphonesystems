<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

class View extends \Amasty\RequestQuote\Controller\Adminhtml\Quote
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::view';

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $quote = $this->initQuote();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($quote) {
            try {
                $resultPage = $this->initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                $resultRedirect->setPath('amasty_quote/quote/index');
                return $resultRedirect;
            }
            $resultPage->getConfig()->getTitle()->prepend(sprintf("#%s", $quote->getIncrementId()));
            return $resultPage;
        }
        $resultRedirect->setPath('amasty_quote/quote/index');
        return $resultRedirect;
    }
}
