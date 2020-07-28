<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Api;

/**
 * @api
 */
interface BarManagementInterface
{
    /**
     * @param int $customerGroup
     * @param string $page
     * @param int[] $position
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function getFilledData($customerGroup, $page, $position);
}
