<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\Order\Email\Items;

use Amasty\Stockstatus\Helper\Data;

class DefaultOrder
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * DefaultOrder constructor.
     * @param Data $helper
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Data $helper
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        if ($this->helper->getModuleConfig('display/display_in_email')) {
            $find   = '<p class="sku';

            $sku = $subject->getItem()->getSku();
            if ($subject->getItem()->getProductType() == 'bundle') {
                $product = $this->productRepository->getById($subject->getItem()->getProductId());
                $sku = $product->getSku();
            }

            $status = $this->helper->getCartStockStatus($sku);
            if ($status) {
                $status = '<p>' . $status . '</p>' ;
                $result = str_replace($find, $status . $find, $result);
            }

        }

        return $result;
    }
}
