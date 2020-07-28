<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Tab;

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
     * @return \Magento\Quote\Model\Quote|mixed
     */
    public function getQuote()
    {
        return $this->getSession()->getParentQuote();
    }

    /**
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('quote_items');
    }

    /**
     * @param int $quoteId
     * @return string
     */
    public function getViewUrl($quoteId)
    {
        return $this->getUrl('amasty_quote/*/*', ['quote' => $quoteId]);
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
