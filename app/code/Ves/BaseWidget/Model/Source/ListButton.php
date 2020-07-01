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

class ListButton implements \Magento\Framework\Option\ArrayInterface
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
                array('value' => "facebook", 'label'=>__('Facebook')),
                  array('value' => "twitter", 'label'=>__('Twitter')),
                  array('value' => "yahoomail", 'label'=>__('Y! Mail')),
                  array('value' => "zingme", 'label'=>__('ZingMe')),
                  array('value' => "pinterest", 'label'=>__('Pinterest Pin It')),
                  array('value' => "print", 'label'=>__('Print')),
                  array('value' => "email", 'label'=>__('Email')),
                  array('value' => "tumblr", 'label'=>__('Tumblr')),
                  array('value' => "linkedin", 'label'=>__('LinkedIn')),
                  array('value' => "favorites", 'label'=>__('Favorites')),
                  array('value' => "gmail", 'label'=>__('Gmail')),
                  array('value' => "google_plusone_share", 'label'=>__('Google+ Share')),
                  array('value' => "hotmail", 'label'=>__('Hotmail')),
                  array('value' => "linkshares", 'label'=>__('Linkshares')),
                  array('value' => "myspace", 'label'=>__('Myspace')),
                  array('value' => "printfriendly", 'label'=>__('PrintFriendly')),
                  array('value' => "virb", 'label'=>__('Virb')),
                  array('value' => "webnews", 'label'=>__('Webnews')),
                  array('value' => "windows", 'label'=>__('Windows Gadgets')),
                  array('value' => "wordpress", 'label'=>__('WordPress')),
                  array('value' => "yigg", 'label'=>__('Yigg')),
                  array('value' => "ziczac", 'label'=>__('ZicZac'))
        );
    }
}
