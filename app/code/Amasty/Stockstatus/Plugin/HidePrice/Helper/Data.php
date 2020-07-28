<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\HidePrice\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Amasty\Stockstatus\Helper\Data as StockStatusHelper;

class Data
{
    /**
     * @var StockStatusHelper
     */
    private $helper;

    /**
     * @var null|ProductInterface
     */
    private $product = null;

    public function __construct(StockStatusHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param bool $result
     * @param ProductInterface $product
     *
     * @return array
     */
    public function beforeCheckStockStatus($subject, $result, $product)
    {
        $this->product = $product;

        return [$result, $product];
    }

    /**
     * @param $subject
     * @param bool $result
     *
     * @return bool
     */
    public function afterCheckStockStatus($subject, $result)
    {
        if ($this->product) {
            if ($this->helper->isHidePrice($this->product)) {
                $result = true;
            }
        }

        return $result;
    }
}
