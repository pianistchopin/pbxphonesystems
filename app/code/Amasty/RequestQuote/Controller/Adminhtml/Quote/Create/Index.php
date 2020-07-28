<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

class Index extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\Create
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->_initSession();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_RequestQuote::manage_quotes');
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Quote'));

        return $resultPage;
    }
}
