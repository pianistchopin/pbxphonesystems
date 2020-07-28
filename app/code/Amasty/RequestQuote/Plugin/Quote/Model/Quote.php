<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Plugin\Quote\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote as QuoteModel;
use Amasty\RequestQuote\Model\ResourceModel\Quote as QuoteResource;
use Amasty\RequestQuote\Model\Source\Status;

class Quote
{
    /**
     * @var QuoteResource
     */
    private $quoteResource;

    public function __construct(QuoteResource $quoteResource)
    {
        $this->quoteResource = $quoteResource;
    }

    /**
     * @param QuoteModel $subject
     * @param mixed $result
     *
     * @return bool
     */
    public function afterGetIsActive($subject, $result)
    {
        return $result;
        /**
         * @TODO remove this and explain, why it was created
         */
        if (!$result) {
            try {
                // detect amasty quote
                $quote = $this->quoteRepository->get($subject->getId());
                $result = Status::CREATED == $quote->getStatus();
            } catch (NoSuchEntityException $exception) {
                $result = false;
            }
        }
    }

    /**
     * @param QuoteModel $subject
     * @param callable $proceed
     *
     * @return QuoteModel
     */
    public function aroundDelete($subject, $proceed)
    {
        //prevent clearing amasty quotes
        if (!$this->quoteResource->isAmastyQuote($subject->getId())) {
            $proceed();
        }

        return $subject;
    }
}
