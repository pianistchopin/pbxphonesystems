<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stockstatus
 */


namespace Amasty\Stockstatus\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class DateFormat implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'F d, Y',
                'label' => 'F d, Y (' . date('F d, Y') . ')'
            ],
            [
                'value' => 'M d, Y',
                'label' => 'M d, Y (' . date('M d, Y') . ')'
            ],
            [
                'value' => 'Y-m-d',
                'label' => 'Y-m-d (' . date('Y-m-d') . ')'
            ],
            [
                'value' => 'm/d/Y',
                'label' => 'm/d/Y (' . date('m/d/Y') . ')'
            ],
            [
                'value' => 'd/m/Y',
                'label' => 'd/m/Y (' . date('d/m/Y') . ')'
            ],
            [
                'value' => 'j/n/y',
                'label' => 'j/n/y (' . date('j/n/y') . ')'
            ],
            [
                'value' => 'j/n/Y',
                'label' => 'j/n/Y (' . date('j/n/Y') . ')'
            ],
            [
                'value' => 'd.m.Y',
                'label' => 'd.m.Y (' . date('d.m.Y') . ')'
            ],
            [
                'value' => 'd.m.y',
                'label' => 'd.m.y (' . date('d.m.y') . ')'
            ],
            [
                'value' => 'j.n.y',
                'label' => 'j.n.y (' . date('j.n.y') . ')'
            ],
            [
                'value' => 'j.n.Y',
                'label' => 'j.n.Y (' . date('j.n.Y') . ')'
            ],
            [
                'value' => 'd-m-y',
                'label' => 'd-m-y (' . date('d-m-y') . ')'
            ],
            [
                'value' => 'Y.m.d',
                'label' => 'Y.m.d (' . date('Y.m.d') . ')'
            ],
            [
                'value' => 'd-m-Y',
                'label' => 'd-m-Y (' . date('d-m-Y') . ')'
            ],
            [
                'value' => 'Y/m/d',
                'label' => 'Y/m/d (' . date('Y/m/d') . ')'
            ],
            [
                'value' => 'y/m/d',
                'label' => 'y/m/d (' . date('y/m/d') . ')'
            ],
            [
                'value' => 'd/m/y',
                'label' => 'd/m/y (' . date('d/m/y') . ')'
            ],
            [
                'value' => 'm/d/y',
                'label' => 'm/d/y (' . date('m/d/y') . ')'
            ],
            [
                'value' => 'd/m Y',
                'label' => 'd/m Y (' . date('d/m Y') . ')'
            ],
            [
                'value' => 'Y m d',
                'label' => 'Y m d (' . date('Y m d') . ')'
            ]
        ];
    }
}
