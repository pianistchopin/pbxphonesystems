<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\Cart;

class AbstractCart
{
    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\Stockstatus\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function aroundGetItemHtml(
        \Magento\Checkout\Block\Cart\AbstractCart $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $result = $proceed($item);
        if ($this->helper->getModuleConfig('display/display_in_cart')) {
            $find   = '</strong>';
            $product = $item->getProduct();
            $status = $this->helper->getProductStockStatus($product, $item);

            if ($status) {
                $status = '<div class="amstockstatus-cart">' .
                    $status . $this->helper->getInfoBlock() .
                    '</div>';
                $result = str_replace($find, $find . $status, $result);
            }
        }

        return $result;
    }
}

