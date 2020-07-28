<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Plugin\InventoryConfigurableProduct\Plugin\Model\ResourceModel\Attribute;

use Magento\InventoryConfigurableProduct\Plugin\Model\ResourceModel\Attribute\IsSalableOptionSelectBuilder as NativeIsSalableOptionSelectBuilder;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use Amasty\Stockstatus\Helper\Data;
use Amasty\Stockstatus\Model\Source\Outofstock;

class IsSalableOptionSelectBuilder
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param NativeIsSalableOptionSelectBuilder $subject
     * @param \Closure $proceed
     * @param OptionSelectBuilderInterface $origSubject
     * @param Select $select
     *
     * @return Select
     */
    public function aroundAfterGetSelect(
        NativeIsSalableOptionSelectBuilder $subject,
        \Closure $proceed,
        OptionSelectBuilderInterface $origSubject,
        Select $select
    ) {
        if ($this->helper->getOutofstockVisibility() === Outofstock::MAGENTO_LOGIC) {
            $select = $proceed($origSubject, $select);
        }

        return $select;
    }
}
