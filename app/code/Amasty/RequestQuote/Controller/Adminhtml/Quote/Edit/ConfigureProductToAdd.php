<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Edit;

class ConfigureProductToAdd extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\ActionAbstract
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $configureResult = new \Magento\Framework\DataObject();
        $configureResult->setOk(true);
        $configureResult->setProductId($productId);
        $configureResult->setCurrentStoreId($this->getSession()->getQuote(true)->getStore()->getId());
        $configureResult->setCurrentCustomerId($this->getSession()->getQuote(true)->getCustomerId());

        /** @var \Magento\Catalog\Helper\Product\Composite $helper */
        $helper = $this->_objectManager->get(\Magento\Catalog\Helper\Product\Composite::class);
        return $helper->renderConfigureResult($configureResult);
    }
}
