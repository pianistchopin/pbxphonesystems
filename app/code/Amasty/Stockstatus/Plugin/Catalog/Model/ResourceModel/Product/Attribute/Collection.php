<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\Catalog\Model\ResourceModel\Product\Attribute;

use Magento\Framework\App\RequestInterface;

class Collection
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $subject
     * @param \Closure $proceed
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function aroundAddVisibleFilter(
        $subject,
        \Closure $proceed
    ) {
        if ($this->request->getControllerName() == 'product_set') {
            $subject->addFieldToFilter(
                ['visible' => 'additional_table.is_visible', 'qty_based' => 'main_table.attribute_code'],
                ['visible' => '1', 'qty_based' => 'custom_stock_status_qty_based']
            );
        } else {
            $proceed();
        }

        return $subject;
    }
}
