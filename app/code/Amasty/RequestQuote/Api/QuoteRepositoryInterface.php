<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Api;

interface QuoteRepositoryInterface extends \Magento\Quote\Api\CartRepositoryInterface
{
    /**
     * @param $quoteId
     * @return mixed
     */
    public function get($quoteId);

    /**
     * @param int $quoteId
     * @return bool
     */
    public function approve($quoteId);

    /**
     * @param int $quoteId
     * @return bool
     */
    public function expire($quoteId);

    /**
     * Enables administrative users to list carts that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
     */
    public function getRequestsList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $quoteId
     * @param string $note
     * @return bool
     */
    public function addCustomerNote($quoteId, $note);

    /**
     * @param int $quoteId
     * @param string $note
     * @return bool
     */
    public function addAdminNote($quoteId, $note);
}
