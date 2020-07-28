<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Create;

class Store extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Create\AbstractCreate
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_store');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please select a store');
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if ($customer = $this->getCustomer()) {
            $this->getChildBlock('select')->setWebsiteIds(
                [$customer->getWebsiteId()]
            );
        }

        return parent::toHtml();
    }
}
