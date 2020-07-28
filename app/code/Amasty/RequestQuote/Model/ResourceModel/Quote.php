<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Model\ResourceModel;

use Amasty\RequestQuote\Api\Data\QuoteInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\SalesSequence\Model\Manager;
use Amasty\RequestQuote\Model\Source\Status;

class Quote extends \Magento\Quote\Model\ResourceModel\Quote
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Manager $sequenceManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $connectionName = null
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $sequenceManager, $connectionName);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect(
            'customer_id',
            $customerId,
            $quote
        );

        $data = $connection->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $quoteId
     * @return $this;
     */
    public function loadByIdWithoutStore($quote, $quoteId)
    {
        $connection = $this->getConnection();
        if ($connection) {
            $select = $this->_getLoadSelect('entity_id', $quoteId, $quote);

            $data = $connection->fetchRow($select);

            if ($data) {
                $quote->setData($data);
            }
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * @param $quote
     * @param $quoteId
     */
    public function loadMagentoQuoteByIdWithoutStore($quote, $quoteId)
    {
        $connection = $this->getConnection();
        if ($connection) {
            $select = parent::_getLoadSelect('entity_id', $quoteId, $quote);

            $data = $connection->fetchRow($select);

            if ($data) {
                $quote->setData($data);
            }
        }

        $this->_afterLoad($quote);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $quoteId
     * @return $this
     */
    public function loadActive($quote, $quoteId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect('entity_id', $quoteId, $quote)->where('is_active = ?', 1);
        $this->applyActiveFilter($select);

        $data = $connection->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveAmastyQuote($object);
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    private function saveAmastyQuote(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getConnection()->insertOnDuplicate($this->getAmastyQuoteTable(), [
            'quote_id'  => $object->getId(),
            'status'    => $object->getStatus(),
            'increment_id' => $object->prepareIncrementId(),
            'customer_name' => $object->prepareCustomerName(),
            'remarks' => $object->getRemarks(),
            'expired_date' => $object->getExpiredDate(),
            'reminder_date' => $object->getReminderDate(),
            'submited_date' => $this->timezone->date()->format('Y-m-d H:i:s'),
            QuoteInterface::ADMIN_NOTIFICATION_SEND => $object->getAdminNotificationSend()
        ]);
    }

    /**
     * @return string
     */
    public function getAmastyQuoteTable()
    {
        return $this->getTable('amasty_quote');
    }

    /**
     * @param \Zend_Db_Select $select
     */
    private function joinAmastyQuote($select)
    {
        $select->joinInner(
            ['amquote' => $this->getAmastyQuoteTable()],
            "amquote.quote_id = " . $this->getMainTable() . ".entity_id",
            [
                'status',
                'remarks',
                'increment_id',
                'customer_name',
                'expired_date',
                'reminder_date',
                'submited_date',
                QuoteInterface::ADMIN_NOTIFICATION_SEND
            ]
        )
            ->order('updated_at ' . \Magento\Framework\DB\Select::SQL_DESC)
            ->limit(1);
    }

    /**
     * @param \Zend_Db_Select $select
     */
    private function applyActiveFilter($select)
    {
        $select->where('amquote.status =? ', Status::CREATED);
    }

    /**
     * @inheritdoc
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->joinAmastyQuote($select);

        return $select;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function processNotModifiedSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveAmastyQuote($object);
        return parent::processNotModifiedSave($object);
    }

    /**
     * @param \Amasty\RequestQuote\Model\Quote $quote
     * @param $status
     * @return mixed
     */
    public function updateStatus(\Amasty\RequestQuote\Model\Quote $quote, $status)
    {
        $this->getConnection()->insertOnDuplicate($this->getAmastyQuoteTable(), [
            'quote_id'  => $quote->getId(),
            'status'    => $status
        ], ['status']);
        return $this;
    }

    /**
     * @param int $quoteId
     *
     * @return bool
     */
    public function isAmastyQuote($quoteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getAmastyQuoteTable())
            ->where('quote_id = ?', $quoteId);
        // detect amasty quote
        $result = (bool)$connection->fetchRow($select);

        return $result;
    }

    /**
     * @param \Amasty\RequestQuote\Model\Quote $quote
     * @param $remarks
     * @return mixed
     */
    public function updateRemarks(\Amasty\RequestQuote\Model\Quote $quote, $remarks)
    {
        $this->getConnection()->insertOnDuplicate($this->getAmastyQuoteTable(), [
            'quote_id' => $quote->getId(),
            'remarks' => $remarks
        ], ['remarks']);
        return $this;
    }
}
