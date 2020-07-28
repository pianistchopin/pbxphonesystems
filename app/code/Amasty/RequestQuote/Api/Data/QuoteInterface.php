<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_RequestQuote
 */


namespace Amasty\RequestQuote\Api\Data;

use \Magento\Quote\Model\Quote as MagentoQuote;

/**
 * Interface QuoteInterface
 */
interface QuoteInterface
{
    /**
     * @TODO need to add extends of  \Magento\Quote\Api\Data\CartInterface
     */
    const ADMIN_NOTIFICATION_SEND = 'admin_notification_send';
    const ADMIN_NOTE_KEY = 'admin_note';
    const CUSTOMER_NOTE_KEY = 'customer_note';
}
