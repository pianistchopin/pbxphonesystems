<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Api;

/**
 * Interface QuoteItemRepositoryInterface
 */
interface QuoteItemRepositoryInterface
{
    /**
     * @param int $quoteItemId
     * @param string $note
     * @return bool
     */
    public function addCustomerNote($quoteItemId, $note);

    /**
     * @param int $quoteItemId
     * @param string $note
     * @return bool
     */
    public function addAdminNote($quoteItemId, $note);
}
