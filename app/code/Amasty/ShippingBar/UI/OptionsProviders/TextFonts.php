<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\UI\OptionsProviders;

class TextFonts implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Arial, Helvetica, sans-serif',
                'label' => 'Arial, Helvetica',
            ],
            [
                'value' => "'Arial Black', Gadget, sans-serif",
                'label' => 'Arial Black, Gadget',
            ],
            [
                'value' => "'Courier New', Courier, monospace",
                'label' => 'Courier New, Courier',
            ],
            [
                'value' => 'Georgia, serif',
                'label' => 'Georgia',
            ],
            [
                'value' => "'MS Sans Serif', Geneva, sans-serif",
                'label' => 'MS Sans Serif, Geneva',
            ],
            [
                'value' => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
                'label' => 'Palatino Linotype, Book Antiqua',
            ],
            [
                'value' => 'Tahoma, Geneva, sans-serif',
                'label' => 'Tahoma, Geneva',
            ],
            [
                'value' => "'Times New Roman', Times, serif",
                'label' => 'Times New Roman, Times',
            ],
            [
                'value' => "'Trebuchet MS', Helvetica, sans-serif",
                'label' => 'Trebuchet MS, Helvetica',
            ],
            [
                'value' => 'Verdana, Geneva, sans-serif',
                'label' => 'Verdana, Geneva',
            ],
        ];
    }
}
