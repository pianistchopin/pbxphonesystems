<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Store\Model\ResourceModel\Website\Collection;

class Table extends \Magento\Backend\Block\Template
{
    /**
     * @var Collection|null
     */
    protected $_websiteCollection = null;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals_table');
    }
}
