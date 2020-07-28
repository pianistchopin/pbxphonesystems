<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

class Customer extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_customer');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please select a customer');
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        // TODO: add create new customer button
        return '';
    }
}
