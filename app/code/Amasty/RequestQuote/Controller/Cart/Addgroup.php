<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Cart;

use Magento\Sales\Model\Order\Item;

class Addgroup extends \Amasty\RequestQuote\Controller\Cart
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $orderItemIds = $this->getRequest()->getPost('order_items');
        if (is_array($orderItemIds)) {
            $itemsCollection = $this->_objectManager->create(\Magento\Sales\Model\Order\Item::class)
                ->getCollection()
                ->addIdFilter($orderItemIds)
                ->load();
            /* @var $itemsCollection \Magento\Sales\Model\ResourceModel\Order\Item\Collection */
            foreach ($itemsCollection as $item) {
                try {
                    $this->addOrderItem($item);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    if ($this->checkoutSession->getUseNotice(true)) {
                        $this->messageManager->addNotice($e->getMessage());
                    } else {
                        $this->messageManager->addError($e->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addException(
                        $e,
                        __('We can\'t add this item to your shopping cart right now.')
                    );
                    return $this->_goBack();
                }
            }
            $this->cart->save();
        }
        return $this->_goBack();
    }

    /**
     * @param Item $item
     * @return void
     */
    private function addOrderItem(Item $item)
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = $this->cart->getCustomerSession();
        if ($session->isLoggedIn()) {
            $orderCustomerId = $item->getQuote()->getCustomerId();
            $currentCustomerId = $session->getCustomer()->getId();
            if ($orderCustomerId == $currentCustomerId) {
                $this->cart->addOrderItem($item, 1);
            }
        }
    }
}
