<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\View;

class Items extends \Amasty\RequestQuote\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid parent block for this block'));
        }
        $this->setQuote($this->getParentBlock()->getQuote());
        parent::_beforeToHtml();
    }

    /**
     * @return \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    public function getItemsCollection()
    {
        return $this->getQuote()->getItemsCollection();
    }
}
