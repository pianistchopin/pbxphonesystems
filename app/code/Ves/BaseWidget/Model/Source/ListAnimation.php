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

class ListAnimation implements \Magento\Framework\Option\ArrayInterface
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
                  array('value' => "", 'label'=>__('No Animation')),
                  array('value' => "bounce", 'label'=>__('bounce')),
                  array('value' => "flash", 'label'=>__('flash')),
                  array('value' => "pulse", 'label'=>__('pulse')),
                  array('value' => "rubberBand", 'label'=>__('rubberBand')),
                  array('value' => "shake", 'label'=>__('shake')),
                  array('value' => "swing", 'label'=>__('swing')),
                  array('value' => "tada", 'label'=>__('tada')),
                  array('value' => "wobble", 'label'=>__('wobble')),
                  array('value' => "bounceIn", 'label'=>__('bounceIn')),
                  array('value' => "bounceInDown", 'label'=>__('bounceInDown')),
                  array('value' => "bounceInLeft", 'label'=>__('bounceInLeft')),
                  array('value' => "bounceInRight", 'label'=>__('bounceInRight')),
                  array('value' => "bounceInUp", 'label'=>__('bounceInUp')),
                  array('value' => "fadeIn", 'label'=>__('fadeIn')),
                  array('value' => "fadeInDown", 'label'=>__('fadeInDown')),
                  array('value' => "fadeInDownBig", 'label'=>__('fadeInDownBig')),
                  array('value' => "fadeInLeft", 'label'=>__('fadeInLeft')),
                  array('value' => "fadeInLeftBig", 'label'=>__('fadeInLeftBig')),
                  array('value' => "fadeInRight", 'label'=>__('fadeInRight')),
                  array('value' => "fadeInRightBig", 'label'=>__('fadeInRightBig')),
                  array('value' => "fadeInUp", 'label'=>__('fadeInUp')),
                  array('value' => "fadeInUpBig", 'label'=>__('fadeInUpBig')),
                  array('value' => "flip", 'label'=>__('flip')),
                  array('value' => "flipInX", 'label'=>__('flipInX')),
                  array('value' => "flipInY", 'label'=>__('flipInY')),
                  array('value' => "lightSpeedIn", 'label'=>__('lightSpeedIn')),
                  array('value' => "rotateIn", 'label'=>__('rotateIn')),
                  array('value' => "rotateInDownLeft", 'label'=>__('rotateInDownLeft')),
                  array('value' => "rotateInDownRight", 'label'=>__('rotateInDownRight')),
                  array('value' => "rotateInUpLeft", 'label'=>__('rotateInUpLeft')),
                  array('value' => "rotateInUpRight", 'label'=>__('rotateInUpRight')),
                  array('value' => "hinge", 'label'=>__('hinge')),
                  array('value' => "rollIn", 'label'=>__('rollIn')),
                  array('value' => "zoomIn", 'label'=>__('zoomIn')),
                  array('value' => "zoomInDown", 'label'=>__('zoomInDown')),
                  array('value' => "zoomInLeft", 'label'=>__('zoomInLeft')),
                  array('value' => "zoomInRight", 'label'=>__('zoomInRight')),
                  array('value' => "zoomInUp", 'label'=>__('zoomInUp')),
                  );
    }
}
