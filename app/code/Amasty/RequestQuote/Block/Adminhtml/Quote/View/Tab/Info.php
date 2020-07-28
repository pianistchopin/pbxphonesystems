<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\View\Tab;

class Info extends \Amasty\RequestQuote\Block\Adminhtml\Quote\AbstractQuote implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getSource()
    {
        return $this->getQuote();
    }

    /**
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('quote_items');
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/*/*', ['order_id' => $orderId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Quote Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
