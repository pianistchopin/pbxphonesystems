<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search\Grid\Renderer;

class Qty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    private $typeConfig;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->typeConfig = $typeConfig;
    }

    /**
     * @param $row
     * @return bool
     */
    private function isInactive($row)
    {
        return $this->typeConfig->isProductSet($row->getTypeId());
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        // Prepare values
        $disabled = '';
        $addClass = '';

        if ($this->isInactive($row)) {
            $qty = '';
            $disabled = 'disabled="disabled" ';
            $addClass = ' input-inactive';
        } else {
            $qty = $row->getData($this->getColumn()->getIndex());
            $qty *= 1;
            if (!$qty) {
                $qty = '';
            }
        }

        // Compose html
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $qty . '" ' . $disabled;
        $html .= 'list_type="" ';
        $html .= 'product_id="' . $row->getId() . '" ';
        $html .= 'class="input-text admin__control-text ' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
