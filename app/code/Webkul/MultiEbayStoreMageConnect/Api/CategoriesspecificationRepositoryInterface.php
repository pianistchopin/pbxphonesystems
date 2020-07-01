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
interface CategoriesspecificationRepositoryInterface
{
    /**
     * get collection by ebay category id
     * @param  int $ebayCateId
     * @return object
     */
    public function getCollectionByeBayCatId($ebayCateId);
}
