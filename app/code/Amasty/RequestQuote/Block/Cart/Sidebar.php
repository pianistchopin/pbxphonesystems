<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Cart;

use Magento\Store\Model\ScopeInterface;

class Sidebar  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Amasty\RequestQuote\Helper\Data $helperData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }
        unset($this->jsLayout['components']['minicart_content']);
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->context = $context;
        $this->imageHelper = $imageHelper;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return string
     */
    public function getQuoteCartUrl()
    {
        return $this->getUrl('amasty_quote/cart');
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->helperData->isAllowedCustomerGroup() && $this->helperData->isActive();
    }

    /**
     * @return string
     */
    public function getSerializedConfig()
    {
        return $this->jsonEncoder->encode($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            // 'checkoutUrl' name need in sidebar.js for "My Quote Cart" button
            'checkoutUrl' => $this->getQuoteCartUrl(),
            'updateItemQtyUrl' => $this->getUpdateItemQtyUrl(),
            'removeItemUrl' => $this->getRemoveItemUrl(),
            'imageTemplate' => $this->getImageHtmlTemplate(),
            'baseUrl' => $this->getBaseUrl(),
            'minicartMaxItemsVisible' => $this->getMiniCartMaxItemsCount(),
            'websiteId' => $this->_storeManager->getStore()->getWebsiteId(),
            'maxItemsToDisplay' => $this->getMaxItemsToDisplay()
        ];
    }

    /**
     * @return string
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->getUrl('amasty_quote/sidebar/updateItemQty', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * @return string
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('amasty_quote/sidebar/removeItem', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * @return string
     */
    public function getImageHtmlTemplate()
    {
        return $this->imageHelper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';
    }

    /**
     * @return int
     */
    private function getMiniCartMaxItemsCount()
    {
        return (int)$this->_scopeConfig->getValue('checkout/sidebar/count', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    private function getMaxItemsToDisplay()
    {
        return (int)$this->_scopeConfig->getValue(
            'checkout/sidebar/max_items_display_count',
            ScopeInterface::SCOPE_STORE
        );
    }
}
