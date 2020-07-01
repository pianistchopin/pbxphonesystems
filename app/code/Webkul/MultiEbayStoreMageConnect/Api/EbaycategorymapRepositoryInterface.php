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
interface EbaycategorymapRepositoryInterface
{

    /**
     * get ebay mapped category collection by rule id
     * @return object
     */
    public function getCollectionByRuleId($ruleId);

    /**
     * get collection by mage category id
     * @param  int $mageCateId
     * @param  int $ruleId
     * @return object
     */
    public function getCollectionByMageCateIdnRuleId($mageCateId, $ruleId);

    /**
     * get collection by entity ids
     * @param  array $entityIds
     * @return object
     */
    public function getCollectionByIds(array $entityIds);

    /**
     * get record by magento category id
     * @param  array $mageCateIds
     * @return object
     */
    public function getCollectionByMageCateIdsnRuleId($mageCateIds, $ruleId);

    /**
     * get collection by rule id and ebay cate id
     * @param  int $eBayCateId
     * @param  int $ruleId
     * @return object
     */
    public function getCollectionByeBayCateIdnRuleId($eBayCateId, $ruleId);
}
