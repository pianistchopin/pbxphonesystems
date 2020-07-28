<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Amasty\RequestQuote\Controller\Adminhtml\Quote\Create as CreateAction;

class LoadBlock extends CreateAction
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        RawFactory $resultRawFactory,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $session,
        \Amasty\RequestQuote\Model\Quote\Backend\Edit $quoteEditModel,
        \Magento\Quote\Model\CustomerManagement $customerManagement
    ) {
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory,
            $session,
            $quoteEditModel,
            $customerManagement
        );
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $request = $this->getRequest();
        try {
            $this->_initSession()->_processData();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_reloadQuote();
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('amasty_quote_quote_create_load_block_json');
        } else {
            $resultPage->addHandle('amasty_quote_quote_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle('amasty_quote_quote_create_load_block_' . $block);
            }
        }

        $result = $resultPage->getLayout()->renderElement('content');
        if ($request->getParam('as_js_varname')) {
            $this->_getSession()->setUpdateResult($result);
            return $this->resultRedirectFactory->create()->setPath('amasty_quote/*/showUpdateResult');
        }
        return $this->resultRawFactory->create()->setContents($result);
    }
}
