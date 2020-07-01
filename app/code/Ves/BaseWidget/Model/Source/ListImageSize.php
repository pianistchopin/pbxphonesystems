<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2015 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Model\Source;

class ListImageSize implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct() {

    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {   
       return array(
                  array('value' => "s", 'label'=>__('Small square 75x75')),
                  array('value' => "q", 'label'=>__('Large square 150x150')),
                  array('value' => "t", 'label'=>__('Thumbnail, 100 on longest side')),
                  array('value' => "m", 'label'=>__('Small, 240 on longest side')),
                  array('value' => "n", 'label'=>__('Small, 320 on longest side')),
                  array('value' => "z", 'label'=>__('Medium 640, 640 on longest side')),
                  array('value' => "c", 'label'=>__('Medium 800, 800 on longest side†')),
                  array('value' => "b", 'label'=>__('Large, 1024 on longest side*')),
                  );
    }
}
