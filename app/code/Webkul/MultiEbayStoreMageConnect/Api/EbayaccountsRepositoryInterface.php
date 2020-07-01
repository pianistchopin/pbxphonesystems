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
interface EbayaccountsRepositoryInterface
{
    /**
     * @param  int $id
     * @return object
     */
    public function getConfigurationById($id);

    /**
     * get by ebay user id
     *
     * @param string $ebayUserId
     * @return object
     */
    public function getByUserId($ebayUserId);
}
