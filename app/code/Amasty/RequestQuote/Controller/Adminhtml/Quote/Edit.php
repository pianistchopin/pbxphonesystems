<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

class Edit extends \Amasty\RequestQuote\Controller\Adminhtml\Quote
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::edit';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->initQuote(true)) {
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Amasty_RequestQuote::manage_quotes');
            $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Quote'));
            return $resultPage;
        };
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'amasty_quote/quote/view',
            ['quote_id' => $this->getRequest()->getParam('quote_id')]
        );
    }
}
