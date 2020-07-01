<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Testimonial
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Testimonial\Model\Config\Source;
 
class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
        ['value' => 'list', 'label' => __('List')],
        ['value' => 'grid', 'label' => __('Grid')],
        ['value' => 'grid1', 'label' => __('Grid 1')],
        ['value' => 'grid2', 'label' => __('Grid 2')],
        ['value' => 'style1', 'label' => __('Style 1')],
        ['value' => 'style2', 'label' => __('Style 2')],
        ['value' => 'style3', 'label' => __('Style 3')],
        ['value' => 'style4', 'label' => __('Style 4')],
        ['value' => 'slide1', 'label' => __('Slide 1')],
        ['value' => 'slide2', 'label' => __('Slide 2')],
        ['value' => 'topmeta', 'label' => __('Top Meta')],
        ['value' => 'bottommeta', 'label' => __('Bottom Meta')],
        ['value' => 'alltop', 'label' => __('Image And Meta On Top')],
        ['value' => 'allbottom', 'label' => __('Image And Meta On Bottom')],
        ['value' => 'topimage', 'label' => __('Top Image')],
        ['value' => 'bottomimage', 'label' => __('Bottom Image')]
        ];
    }
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
        'grid2'        => __('List'),
        'grid'         => __('Grid'),
        'grid1'        => __('Grid1'),
        'grid2'        => __('Grid2'),
        'style1'       => __('Style 1'),
        'style2'       => __('Style 2'),
        'style3'       => __('Style 3'),
        'style4'       => __('Style 4'),
        'style1'       => __('Slide 1'),
        'style1'       => __('Slide 2'),
        'topmeta'      => __('Top Meta'), 
        'bottommeta'   => __('Bottom Meta'),
        'alltop'       => __('Image And Meta On Top'),
        'allbottom'    => __('Image And Meta On Bottom'),
        'centertop'    => __('Center Image And Meta On Top'),
        'centerbottom' => __('Center Image And Meta On Bottom'),
        ];
    }
}