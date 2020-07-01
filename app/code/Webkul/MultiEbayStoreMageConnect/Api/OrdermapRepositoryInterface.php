<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Api;

/**
 * @api
 */
interface OrdermapRepositoryInterface
{

    /**
     * get ebay sync  collection of product by ebay rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId);

    /**
     * get record by ebay order id
     * @param  int $ebayOrderId
     * @return object
     */
    public function getRecordByEbayOrderId($ebayOrderId);

    /**
     * get record by mage order id
     * @param  int $mageOrderId
     * @return object
     */
    public function getRecordByMageOrderId($mageOrderId);

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds);

    /**
     * get collection by account id and order id
     * @param  array $entityIds
     * @return object
     */
    public function getByAccountIdnOrderId($accountId, $orderId);
}
