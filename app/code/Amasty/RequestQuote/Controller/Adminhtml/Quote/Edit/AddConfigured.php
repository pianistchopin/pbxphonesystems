<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

class AddConfigured extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $errorMessage = null;
        try {
            $this->initSession()->processActionData();
        } catch (\Exception $e) {
            $this->reloadQuote();
            $errorMessage = $e->getMessage();
        }

        $updateResult = new \Magento\Framework\DataObject();
        if ($errorMessage) {
            $updateResult->setError(true);
            $updateResult->setMessage($errorMessage);
        } else {
            $updateResult->setOk(true);
        }

        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        $this->backendSession->setCompositeProductResult($updateResult);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('catalog/product/showUpdateResult');
    }
}
