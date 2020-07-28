<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\Catalog\Model\ResourceModel\Eav;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute as NativeAttribute;

class Attribute
{
    public function afterGetIsVisible(NativeAttribute $subject, $result)
    {
        if ($subject->getAttributeCode() == 'custom_stock_status_qty_based') {
            $result = true;
        }

        return $result;
    }
}
