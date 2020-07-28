<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */


namespace Amasty\Pgrid\Plugin\Ui\Model;

class Manager extends AbstractReader
{
    /**
     * Added settings for product grid
     *
     * @param \Magento\Ui\Model\Manager $subject
     * @param array                     $result
     *
     * @return array
     */
    public function afterGetData(
        \Magento\Ui\Model\Manager $subject,
        $result
    ) {
        if (isset($result['product_listing']['children'])) {
            $result['product_listing']['children'] = $this->addAmastySettings($result['product_listing']['children']);
        }

        return $result;
    }
}
