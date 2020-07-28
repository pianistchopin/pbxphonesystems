<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

class Start extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::edit';

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->getSession()->clearStorage();
        if ($this->initQuote()) {
            $quote = $this->getSession()->getQuote();
            try {
                if ($quote->getId()) {
                    $this->getQuoteEditModel()->initFromQuote($quote);
                    $resultRedirect->setPath('amasty_quote/quote/edit', ['quote_id' => $quote->getId()]);
                } else {
                    $resultRedirect->setPath('amasty_quote/quote/create_start');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $quote->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
                $resultRedirect->setPath('amasty_quote/quote/view', ['quote_id' => $quote->getId()]);
            }
        } else {
            $resultRedirect->setPath(
                'amasty_quote/quote/view',
                ['quote_id' => $this->getRequest()->getParam('quote_id')]
            );
        }
        return $resultRedirect;
    }
}
