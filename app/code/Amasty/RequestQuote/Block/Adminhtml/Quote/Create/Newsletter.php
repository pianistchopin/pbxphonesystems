<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

class Newsletter extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_newsletter');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Newsletter Subscription');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-newsletter-list';
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
