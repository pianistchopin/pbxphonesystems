<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit;

class History extends \Amasty\RequestQuote\Block\Adminhtml\Quote\View\History
{
    /**
     * @return \Amasty\RequestQuote\Model\Quote|\Magento\Quote\Model\Quote|mixed
     */
    public function getQuote()
    {
        return $this->getQuoteSession()->getParentQuote();
    }
}
