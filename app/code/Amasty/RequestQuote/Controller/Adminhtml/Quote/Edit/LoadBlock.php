<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

class LoadBlock extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $validationPassed = false;
        if ($quote = $this->initQuote(true)) {
            $quote->setForcedCurrency($quote->getQuoteCurrency());
            $validationPassed = true;
            try {
                $this->initSession()->processActionData();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->reloadQuote();
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->reloadQuote();
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }

        $request = $this->getRequest();
        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('amasty_quote_load_block_json');
        } else {
            $resultPage->addHandle('amasty_quote_load_block_plain');
        }

        if ($validationPassed && $block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle('amasty_quote_load_block_' . $block);
            }
        } else {
            $layout = $resultPage->getLayout();
            $ajaxRedirect = $layout->createBlock(
                \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Validation\Redirect::class,
                'ajaxRedirect'
            );
            $ajaxRedirect->setRedirectUrl($this->getUrl(
                'amasty_quote/quote/view',
                ['quote_id' => $this->getRequest()->getParam('quote_id')]
            ));
            $ajaxExpired = $layout->createBlock(
                \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Validation\Expired::class,
                'ajaxExpired'
            );
            $layout->getBlock('content')->setChild('ajaxRedirect', $ajaxRedirect);
            $layout->getBlock('content')->setChild('ajaxExpired', $ajaxExpired);
        }

        $result = $resultPage->getLayout()->renderElement('content');
        if ($request->getParam('as_js_varname')) {
            $this->backendSession->setUpdateResult($result);
            return $this->resultRedirectFactory->create()->setPath('amasty_quote/*/showUpdateResult');
        }
        return $this->resultRawFactory->create()->setContents($result);
    }
}
