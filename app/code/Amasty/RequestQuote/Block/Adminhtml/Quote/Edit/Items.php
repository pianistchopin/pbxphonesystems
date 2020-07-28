<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit;

use Magento\Quote\Model\Quote\Item;

class Items extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\AbstractEdit
{
    /**
     * @var array
     */
    protected $_buttons = [];

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_quote_edit_items');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Quote Items');
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * @param array $args
     * @return void
     */
    public function addButton($args)
    {
        $this->_buttons[] = $args;
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';
        // Make buttons to be rendered in opposite order of addition. This makes "Add products" the last one.
        $this->_buttons = array_reverse($this->_buttons);
        foreach ($this->_buttons as $buttonData) {
            $html .= $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Button::class
            )->setData(
                $buttonData
            )->toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getStoreId()) {
            return parent::_toHtml();
        }
        return '';
    }
}
