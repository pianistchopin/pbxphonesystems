<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Search\Grid\Renderer;

class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $rendered = parent::render($row);
        $isConfigurable = $row->canConfigure();
        $style = $isConfigurable ? '' : 'disabled';
        $prodAttributes = $isConfigurable ? sprintf(
            'list_type = "product_to_add" product_id = %s',
            $row->getId()
        ) : 'disabled="disabled"';
        return sprintf(
            '<a href="javascript:void(0)" class="action-configure %s" %s>%s</a>',
            $style,
            $prodAttributes,
            __('Configure')
        ) . $rendered;
    }
}
