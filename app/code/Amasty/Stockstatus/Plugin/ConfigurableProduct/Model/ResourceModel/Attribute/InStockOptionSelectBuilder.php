<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\ConfigurableProduct\Model\ResourceModel\Attribute;

use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use Magento\ConfigurableProduct\Plugin\Model\ResourceModel\Attribute\InStockOptionSelectBuilder as NativeBuilder;
use Amasty\Stockstatus\Helper\Data;
use Amasty\Stockstatus\Model\Source\Outofstock;

class InStockOptionSelectBuilder
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * InStockOptionSelectBuilder constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Disable Magento stock filter
     *
     * @param NativeBuilder $nativeSubject
     * @param \Closure $proceed
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     */
    public function aroundAfterGetSelect(
        NativeBuilder $nativeSubject,
        \Closure $proceed,
        OptionSelectBuilderInterface $subject,
        Select $select
    ) {
        if ($this->helper->getOutofstockVisibility() === Outofstock::MAGENTO_LOGIC) {
            $select = $proceed($subject, $select);
        }

        return $select;
    }
}
