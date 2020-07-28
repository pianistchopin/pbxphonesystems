<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Plugin\Product;

class Status
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

    public function afterToHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $result
    ) {
        $name = $subject->getNameInLayout();
        $matchedNames = [
            'product.info.configurable',
            'product.info.simple',
            'product.info.bundle',
            'product.info.virtual',
            'product.info.downloadable',
            'product.info.grouped.stock'
        ];

        if (in_array($name, $matchedNames)
            || strpos($name, 'product.info.type_schedule_block') !== false
        ) {
            $status = $this->helper->showStockStatus($subject->getProduct(), 1, 0);
            if ($status != '') {
                $result = $status;
                $result .= $this->helper->getInfoBlock();
            }
        }

        return  $result;
    }
}
