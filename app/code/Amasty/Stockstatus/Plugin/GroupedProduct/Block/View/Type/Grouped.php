<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\GroupedProduct\Block\View\Type;

class Grouped
{
    /**
     * @var \Amasty\Stockstatus\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Amasty\Stockstatus\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function aroundGetProductPrice(
        \Magento\GroupedProduct\Block\Product\View\Type\Grouped $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);

        $status = $this->_helper->getCartStockStatus($product->getData('sku'));
        if ($status) {
            $status = '<p>' . $status . '</p>' ;
            $result = $status . $result;
        }
        return $result;
    }
}

