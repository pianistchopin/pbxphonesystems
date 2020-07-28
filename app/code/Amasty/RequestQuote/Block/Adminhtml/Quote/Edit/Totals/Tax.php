<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals;

class Tax extends \Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Totals\DefaultTotals
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/create/totals/tax.phtml';
}
