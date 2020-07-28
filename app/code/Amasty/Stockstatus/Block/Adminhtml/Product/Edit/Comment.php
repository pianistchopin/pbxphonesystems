<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Block\Adminhtml\Product\Edit;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class Comment extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry, Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function toHtml()
    {
        $attribute = $this->registry->registry('entity_attribute');
        if ($attribute && $attribute->getAttributeCode() == 'custom_stock_status') {
            return parent::toHtml();
        }
    }
}
