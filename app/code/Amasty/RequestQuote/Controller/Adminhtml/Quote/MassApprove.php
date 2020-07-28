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

class MassApprove extends \Amasty\RequestQuote\Controller\Adminhtml\Quote\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Amasty_RequestQuote::approve';

    /**
     * @var \Amasty\RequestQuote\Model\Email\Sender
     */
    private $emailSender;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $configHelper;

    /**
     * @var \Amasty\RequestQuote\Helper\Date
     */
    private $dateHelper;

    public function __construct(
        Context $context, Filter $filter,
        CollectionFactory $collectionFactory,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        \Amasty\RequestQuote\Helper\Date $dateHelper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->emailSender = $emailSender;
        $this->configHelper = $configHelper;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countUpdatedQuotes = 0;
        foreach ($collection->getItems() as $quote) {
            if (!$quote->canApprove()) {
                continue;
            }
            if ($expDays = $this->configHelper->getExpirationTime()) {
                $quote->setExpiredDate($this->dateHelper->increaseDays($expDays));
            }
            if ($remDays = $this->configHelper->getReminderTime()) {
                $quote->setReminderDate($this->dateHelper->increaseDays($remDays));
            }

            if ($quote->getStatus() == Status::ADMIN_CREATED) {
                $newQuote = true;
            } else {
                $newQuote = false;
            }

            $quote->setStatus(Status::APPROVED);
            $quote->save();

            if ($newQuote) {
                $this->emailSender->sendAdminQuoteEmail($quote);
            } else {
                $this->emailSender->sendApproveEmail($quote);
            }

            $countUpdatedQuotes++;
        }
        $countNonUpdatedQuotes = $collection->count() - $countUpdatedQuotes;

        if ($countNonUpdatedQuotes && $countUpdatedQuotes) {
            $this->messageManager->addErrorMessage(__('%1 quote(s) cannot be updated.', $countNonUpdatedQuotes));
        } elseif ($countNonUpdatedQuotes) {
            $this->messageManager->addErrorMessage(__('You cannot update the quote(s).'));
        }

        if ($countUpdatedQuotes) {
            $this->messageManager->addSuccessMessage(__('We updated %1 quote(s).', $countUpdatedQuotes));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}
