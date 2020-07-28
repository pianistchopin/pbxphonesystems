<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Api;

interface GroupRepositoryInterface
{
    /**
     * @param $groupCode
     * @return false or array
     */
    public function getGroupOptionsIds($groupCode);
}
