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
interface ImportedtmpproductRepositoryInterface
{

    /**
     * get a record by item id and product type
     * @param  string $productType
     * @param  int $itemId
     * @return object
     */
    public function getRecordByItemIdnProductType($productType, $itemId);

    /**
     * get tempdate collection by rule id and product type
     * @param  string $productType
     * @param  int $ruleId
     * @return object
     */
    public function getCollectionByProductTypeAndRuleId($productType, $ruleId);
}
