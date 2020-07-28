<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\Catalog\Model;

use Magento\Catalog\Model\Product as ProductModel;
use Amasty\Stockstatus\Helper\Data;
use Magento\Framework\App\RequestInterface;

class Product
{
    const PRODUCT_VIEW = 'catalog/product/view';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Data $helper,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * Fix overwrite bundle select options by js magento
     * @param ProductModel $subject
     * @param string $result
     * @return string
     */
    public function afterGetName($subject, $result)
    {
        /** Check if product is an bundle selection */
        if ($subject->getSelectionCanChangeQty() !== null
            && strpos($this->request->getPathInfo(), self::PRODUCT_VIEW) !== false
            && ($stockStatus = $this->helper->getCustomStockStatusText($subject))
        ) {
            $stockStatus = strip_tags($stockStatus);
            $result .= ' (' . $stockStatus . ')';
        }

        return $result;
    }
}
