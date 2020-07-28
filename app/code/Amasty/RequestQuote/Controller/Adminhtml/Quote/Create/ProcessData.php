<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote\Create;

class ProcessData extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\Create
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->_initSession();
        $this->_processData();
        return $this->resultForwardFactory->create()->forward('index');
    }
}
