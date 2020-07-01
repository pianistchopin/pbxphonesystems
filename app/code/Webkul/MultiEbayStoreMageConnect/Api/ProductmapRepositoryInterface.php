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
interface ProductmapRepositoryInterface
{

    /**
     * get ebay sync  collection of product by ebay rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId);

    /**
     * get collection by ebay product id
     * @param  int $ebayProductId
     * @return object
     */
    public function getRecordByEbayProductId($ebayProductId);

    /**
     * get record by magento product id
     * @param  int $mageProductId
     * @return object
     */
    public function getRecordByMageProductId($mageProductId);

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds);
}
