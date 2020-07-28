<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\ResourceModel\Quote;

class Collection extends \Magento\Quote\Model\ResourceModel\Quote\Collection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(\Amasty\RequestQuote\Model\Quote::class, \Amasty\RequestQuote\Model\ResourceModel\Quote::class);
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->join(
            ['amasty_quote' => $this->getResource()->getAmastyQuoteTable()],
            'amasty_quote.quote_id = main_table.entity_id',
            ['status', 'remarks', 'increment_id', 'customer_name', 'expired_date', 'reminder_date', 'submited_date']
        );

        parent::_renderFiltersBefore();

    }
}
