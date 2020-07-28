<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

class ConfigureQuoteItems extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $configureResult = new \Magento\Framework\DataObject();
        try {
            $quoteItemId = (int)$this->getRequest()->getParam('id');
            if (!$quoteItemId) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Quote item id is not received.'));
            }

            $quoteItem = $this->_objectManager->create(\Magento\Quote\Model\Quote\Item::class)->load($quoteItemId);
            if (!$quoteItem->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Quote item is not loaded.'));
            }

            $configureResult->setOk(true);
            $optionCollection = $this->_objectManager->create(\Magento\Quote\Model\Quote\Item\Option::class)
                ->getCollection()
                ->addItemFilter([$quoteItemId]);
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
            $configureResult->setCurrentCustomerId($this->getSession()->getQuote()->getCustomerId());
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        $helper = $this->_objectManager->get(\Magento\Catalog\Helper\Product\Composite::class);
        return $helper->renderConfigureResult($configureResult);
    }
}
