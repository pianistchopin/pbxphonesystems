<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Multishipping\Model\Checkout\Type;

use Magento\Multishipping\Model\Checkout\Type\Multishipping as NativeMultishipping;

class Multishipping
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param NativeMultishipping $subject
     * @param \Closure $proceed
     * @param $addressId
     * @param $itemId
     *
     * @return NativeMultishipping
     */
    public function aroundRemoveAddressItem(
        NativeMultishipping $subject,
        \Closure $proceed,
        $addressId,
        $itemId
    ) {
        $address = $subject->getQuote()->getAddressById($addressId);
        /* @var $address \Magento\Quote\Model\Quote\Address */
        if ($address) {
            $item = $address->getValidItemById($itemId);
            if ($item &&
                $subject->getQuote()->getItemById($item->getQuoteItemId())->getOptionByCode('amasty_quote_price')
            ) {
                $this->messageManager->addErrorMessage(
                    __('It is not possible to edit items qty of the approved Quote.')
                );
            } else {
                $proceed($addressId, $itemId);
            }
        }

        return $subject;
    }
}
