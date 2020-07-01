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
interface EbaycategoryRepositoryInterface
{

    /**
     * get ebay category collectionby ebay category id
     * @return object
     */
    public function getCollectionByEbayCateId($ebayCateId);

    /**
     * get Collection by ebay parent id
     * @param  int $eBayParentId
     * @return object
     */
    public function getCollectionByeBayCateParentId($eBayParentId);
}
