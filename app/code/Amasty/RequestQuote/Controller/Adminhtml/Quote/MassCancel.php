<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Controller\Adminhtml\Quote;

use Amasty\RequestQuote\Model\Source\Status;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Amasty\RequestQuote\Model\ResourceModel\Quote\CollectionFactory;

class MassCancel extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::close';

    /**
     * @var \Amasty\RequestQuote\Model\Email\Sender
     */
    private $emailSender;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context, Filter $filter,
        CollectionFactory $collectionFactory,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->emailSender = $emailSender;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countCancelQuotes = 0;
        foreach ($collection->getItems() as $quote) {
            if (!$quote->canClose()) {
                continue;
            }
            $quote->setStatus(Status::CANCELED);
            $quote->save();

            $this->emailSender->sendDeclineEmail($quote);

            $countCancelQuotes++;
        }
        $countNonCancelQuote = $collection->count() - $countCancelQuotes;

        if ($countNonCancelQuote && $countCancelQuotes) {
            $this->messageManager->addErrorMessage(__('%1 quote(s) cannot be canceled.', $countNonCancelQuote));
        } elseif ($countNonCancelQuote) {
            $this->messageManager->addErrorMessage(__('You cannot cancel the quote(s).'));
        }

        if ($countCancelQuotes) {
            $this->messageManager->addSuccessMessage(__('We canceled %1 quote(s).', $countCancelQuotes));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
