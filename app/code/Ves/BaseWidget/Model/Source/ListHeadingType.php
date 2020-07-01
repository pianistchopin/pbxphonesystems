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

class ListHeadingType implements \Magento\Framework\Option\ArrayInterface
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
                  array('value' => "", 'label'=>__('Default')),
                  array('value' => "1", 'label'=>__('H1')),
                  array('value' => "2", 'label'=>__('H2')),
                  array('value' => "3", 'label'=>__('H3')),
                  array('value' => "4", 'label'=>__('H4')),
                  array('value' => "5", 'label'=>__('H5')),
                  array('value' => "6", 'label'=>__('H6'))
                  );
    }
}
