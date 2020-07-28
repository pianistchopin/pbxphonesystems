<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Checkout\Controller\Cart;

use Magento\Checkout\Controller\Cart\Configure as NativeConfigure;

class Configure
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->cart = $cart;
        $this->request = $request;
        $this->redirect = $redirect;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param NativeConfigure $subject
     * @param \Closure $proceed
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(NativeConfigure $subject, \Closure $proceed)
    {
        $id = (int)$this->request->getParam('id');
        if ($id) {
            $quoteItem = $this->cart->getQuote()->getItemById($id);
            if ($quoteItem && $quoteItem->getOptionByCode('amasty_quote_price')) {
                $this->messageManager->addNoticeMessage(__('Can\'t update item which moved from quote request.'));
                return $this->redirectFactory->create()->setPath($this->redirect->getRefererUrl());
            }
        }

        return $proceed();
    }
}
