<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */
namespace Amasty\Stockstatus\Model\Source;

class BackOrder
{
    const BACKORDERS_INCREMENT = 100000;
    const BACKORDERS_NO = 100000;
    const BACKORDERS_YES_NONOTIFY = 100001;
    const BACKORDERS_YES_NOTIFY = 100002;

    static public function toArray()
    {
        return [
            [
                'option_id' => self::BACKORDERS_NO,
                'value'     => __('No Backorders (System Value - processed automatically)')
            ],
            [
                'option_id' => self::BACKORDERS_YES_NONOTIFY,
                'value'     => __('Allow Qty Below 0 (System Value - processed automatically)')
            ],
            [
                'option_id' => self::BACKORDERS_YES_NOTIFY,
                'value'     => __('Allow Qty Below 0 and Notify Customer (System Value - processed automatically)')
            ]
        ];
    }
}
