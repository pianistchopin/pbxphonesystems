<?php
namespace Ves\BaseWidget\Model\Source;
 
class ListWidgetWidth implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
        	['value' => 'auto', 'label' => __('Auto')],
            ['value' => '100%', 'label' => __('100%')],
            ['value' => '75%', 'label' => __('75%')],
            ['value' => '50%', 'label' => __('50%')],
            ['value' => '25%', 'label' => __('25%')],
            ['value' => '15%', 'label' => __('15%')]
            ];
    }
}