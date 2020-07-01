<?php

namespace Ves\PageBuilder\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsaveafter implements ObserverInterface
{
    /**
     * @var \Magento\Authorizenet\Model\Directpost
     */
    protected $resourceBlock;

    protected $_request;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Authorizenet\Model\Directpost $payment
     * @param \Magento\Authorizenet\Model\Directpost\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ves\PageBuilder\Model\ResourceModel\Block $resourceBlock
    ) {
        $this->resourceBlock = $resourceBlock;
        $this->_request = $context->getRequest();
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product_id = $observer->getProduct()->getId();  // you will get product object

        /**
         * Perform any actions you want here
         *
         */
        /*
        $post_data = $this->_getRequest()->getPostValue();
        $block_id =  isset($post_data['product']['block_id'])?(int)$post_data['product']['block_id']:0;
        $store = $this->_getRequest()->getParam('store');
        
        $block_builder_profile = $this->resourceBlock->saveProduct($block_id, $_product_id, $store);
        */
        /**
         * Uncomment the line below to save the product
         *
         */
    }
    /**
     * Shortcut to _getRequest
     *
     */
    protected function _getRequest()
    {
        return $this->_request;
    }
}