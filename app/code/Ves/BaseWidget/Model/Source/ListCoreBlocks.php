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

class ListCoreBlocks implements \Magento\Framework\Option\ArrayInterface
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
                  array('value' => "top.links", 'label'=>__('Top Links')),
                  array('value' => "catalog.topnav", 'label'=>__('Top Navigation')),
                  array('value' => "breadcrumbs", 'label'=>__('Breadcrumbs')),
                  array('value' => "wishlist_sidebar", 'label'=>__('Wishlist Sidebar')),
                  array('value' => "form.subscribe", 'label'=>__('Newsletter')),
                  array('value' => "top.search", 'label'=>__('Top Search')),
                  array('value' => "catalog.compare.sidebar", 'label'=>__('Catalog Compare Sidebar')),
                  array('value' => "footer_links", 'label'=>__('Footer Links')),
                  array('value' => "copyright", 'label'=>__('Copyright')),
                  array('value' => "category.products", 'label'=>__('Main Content - Category Products(grid/list)'))
                );
    }
}
