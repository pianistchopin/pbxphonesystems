<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\Email;

use Amasty\RequestQuote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Amasty\RequestQuote\Model\Source\Status;
use Zend_Db_Select;

class Proposal
{
    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var Sender
     */
    private $emailSender;

    public function __construct(
        QuoteCollectionFactory $quoteCollectionFactory,
        Sender $emailSender
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->emailSender = $emailSender;
    }

    public function notify()
    {
        $quoteCollection = $this->quoteCollectionFactory->create()
            ->addFieldToFilter('amasty_quote.status', Status::APPROVED);
        $quoteCollection->getSelect()
            ->columns([
                'need_expired_send' => new \Zend_Db_Expr(
                    'IF(DATE_FORMAT(expired_date, \'%Y-%m-%d %H:%i\') <= DATE_FORMAT(NOW(), \'%Y-%m-%d %H:%i\'), 1, 0)'
                ),
                'need_reminder_send' => new \Zend_Db_Expr(
                    'IF(HOUR(TIMEDIFF(NOW(), reminder_date)) < 1, 1, 0)'
                )
            ])
            ->where('(DATE_FORMAT(expired_date, \'%Y-%m-%d %H:%i\') <= DATE_FORMAT(NOW(), \'%Y-%m-%d %H:%i\')' .
                ' OR HOUR(TIMEDIFF(NOW(), reminder_date)) < 1)');
        /** @var \Amasty\RequestQuote\Model\Quote $quote */
        foreach ($quoteCollection as $quote) {
            if ($quote->getNeedReminderSend()) {
                $this->emailSender->sendReminderEmail($quote);
            }
            if ($quote->getNeedExpiredSend()) {
                $quote->setStatus(Status::EXPIRED);
                $quote->save();
                $this->emailSender->sendExpiredEmail($quote);
            }
        }
    }
}
