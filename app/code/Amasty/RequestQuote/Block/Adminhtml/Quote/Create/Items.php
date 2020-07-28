<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

use Magento\Quote\Model\Quote\Item;

class Items extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
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
        $this->setId('sales_order_create_items');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Items Quoted');
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
