<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Block\Account\Quote;

use Amasty\Base\Model\Serializer;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Amasty\RequestQuote\Model\Source\Status;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var \Amasty\RequestQuote\Helper\Data
     */
    private $configHelper;

    public function __construct(
        TemplateContext $context,
        Registry $registry,
        Serializer $serializer,
        DataObjectFactory $dataObjectFactory,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->serializer = $serializer;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * @return \Amasty\RequestQuote\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('amasty_quote');
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getDeleteUrl($quote)
    {
        return $this->getUrl('amasty_quote/account/delete', ['quote_id' => $quote->getId()]);
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function getCancelUrl($quote)
    {
        return $this->getUrl('amasty_quote/account/cancel', ['quote_id' => $quote->getId()]);
    }

    /**
     * @param \Amasty\RequestQuote\Api\Data\QuoteInterface $quote
     *
     * @return string
     */
    public function isCancelShowed($quote)
    {
        return in_array(
            $quote->getStatus(),
            [
                Status::PENDING,
                Status::APPROVED,
            ]
        );
    }

    /**
     * @return bool
     */
    public function isDeleteShow()
    {
        return false;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getNotes()
    {
        if (!$this->getData('notes')) {
            if ($remarks = $this->getQuote()->getRemarks()) {
                $remarks = $this->serializer->unserialize($remarks);
                $this->setData('notes', $this->dataObjectFactory->create(['data' => $remarks]));
            } else {
                $this->setData('notes', $this->dataObjectFactory->create());
            }
        }
        return $this->getData('notes');
    }

    /**
     * @return bool
     */
    public function isExpiryColumnShow()
    {
        return $this->configHelper->getExpirationTime() !== null
            && in_array($this->getQuote()->getStatus(), [Status::APPROVED, Status::EXPIRED]);
    }

    /**
     * @return string
     */
    public function getExpiredDate()
    {
        $result = __('N/A');
        if ($this->getQuote()->getExpiredDate()) {
            $result = $this->formatDate(
                $this->getQuote()->getExpiredDate(),
                \IntlDateFormatter::MEDIUM,
                true
            );
        }

        return $result;
    }
}
